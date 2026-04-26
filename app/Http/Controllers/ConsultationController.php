<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Appointment;
use App\Models\WalkInQueue;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Branch;
use App\Models\PatientSubscription;
use App\Models\SubscriptionUsage;
use App\Models\PatientMembership;
use App\Models\MembershipUsageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $consultations = Consultation::where('branch_id', $branchId)
            ->with(['patient', 'doctor.user', 'appointment', 'walkInQueue'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('patient', fn($q2) => $q2->where('name', 'like', "%{$request->search}%")
                    ->orWhere('patient_id', 'like', "%{$request->search}%"));
            })
            ->when($request->filled('date'), fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('consultations.index', compact('consultations'));
    }

    public function start(Request $request)
    {
        $branchId = session('current_branch_id');
        if (!$branchId) {
            return back()->with('error', 'Please select a branch first.');
        }

        $patientId = $request->patient_id;
        $doctorId = $request->doctor_id;
        $appointmentId = $request->appointment_id;
        $walkInQueueId = $request->walk_in_queue_id;

        // Pull doctor/patient from queue or appointment
        if ($walkInQueueId) {
            $queue = WalkInQueue::findOrFail($walkInQueueId);
            $patientId = $queue->patient_id;
            $doctorId = $queue->doctor_id;
            $appointmentId = $queue->appointment_id;

            if ($queue->consultation) {
                return redirect()->route('consultations.edit', $queue->consultation);
            }
        } elseif ($appointmentId) {
            $appointment = Appointment::findOrFail($appointmentId);
            $patientId = $appointment->patient_id;
            $doctorId = $appointment->doctor_id;

            if ($appointment->consultation) {
                return redirect()->route('consultations.edit', $appointment->consultation);
            }
        }

        if (!$patientId || !$doctorId) {
            return back()->with('error', 'Patient and doctor are required to start a consultation.');
        }

        $branch = Branch::find($branchId);
        $consultation = Consultation::create([
            'consultation_number' => Consultation::generateNumber($branch->code ?? 'BR'),
            'branch_id' => $branchId,
            'patient_id' => $patientId,
            'doctor_id' => $doctorId,
            'appointment_id' => $appointmentId,
            'walk_in_queue_id' => $walkInQueueId,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        if ($appointmentId) {
            Appointment::where('id', $appointmentId)->update(['status' => 'in_progress']);
        }

        return redirect()->route('consultations.edit', $consultation)
            ->with('success', "Consultation {$consultation->consultation_number} started.");
    }

    public function create(Request $request)
    {
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->with('user')->get();

        $patientMap = $patients->mapWithKeys(fn($p) => [
            $p->id => [
                'name' => $p->name,
                'patient_id' => $p->patient_id,
                'phone' => $p->phone,
                'allergies' => $p->allergies,
                'gender' => $p->gender,
                'blood_type' => $p->blood_type,
                'age' => $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age : null,
            ],
        ])->all();

        $doctorMap = $doctors->mapWithKeys(fn($d) => [
            $d->id => [
                'name' => $d->user->name,
                'specialization' => $d->specialization ?? 'General Practice',
                'fee' => (float) ($d->consultation_fee ?? 0),
                'mmc' => $d->mmc_number,
            ],
        ])->all();

        return view('consultations.create', compact('patients', 'doctors', 'patientMap', 'doctorMap'));
    }

    public function edit(Consultation $consultation)
    {
        $consultation->load(['patient', 'doctor.user', 'appointment', 'walkInQueue', 'prescriptions', 'labReports']);
        return view('consultations.edit', compact('consultation'));
    }

    public function update(Request $request, Consultation $consultation)
    {
        $validated = $request->validate([
            'bp_systolic' => 'nullable|numeric|min:0|max:300',
            'bp_diastolic' => 'nullable|numeric|min:0|max:200',
            'pulse' => 'nullable|numeric|min:0|max:300',
            'temperature' => 'nullable|numeric|min:25|max:45',
            'weight_kg' => 'nullable|numeric|min:0|max:500',
            'height_cm' => 'nullable|numeric|min:0|max:300',
            'spo2' => 'nullable|numeric|min:0|max:100',
            'respiratory_rate' => 'nullable|numeric|min:0|max:80',
            'chief_complaint' => 'nullable|string',
            'history' => 'nullable|string',
            'examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date|after_or_equal:today',
            'mc_issued' => 'nullable|boolean',
            'mc_from' => 'nullable|date|required_if:mc_issued,1',
            'mc_to' => 'nullable|date|after_or_equal:mc_from|required_if:mc_issued,1',
            'mc_reason' => 'nullable|string|max:255',
        ]);

        // Auto BMI
        if (!empty($validated['weight_kg']) && !empty($validated['height_cm']) && $validated['height_cm'] > 0) {
            $hM = $validated['height_cm'] / 100;
            $validated['bmi'] = round($validated['weight_kg'] / ($hM * $hM), 2);
        } else {
            $validated['bmi'] = null;
        }

        // MC days
        $validated['mc_issued'] = $request->boolean('mc_issued');
        if ($validated['mc_issued'] && !empty($validated['mc_from']) && !empty($validated['mc_to'])) {
            $from = \Carbon\Carbon::parse($validated['mc_from']);
            $to = \Carbon\Carbon::parse($validated['mc_to']);
            $validated['mc_days'] = $from->diffInDays($to) + 1;
        } else {
            $validated['mc_from'] = null;
            $validated['mc_to'] = null;
            $validated['mc_days'] = null;
            $validated['mc_reason'] = null;
        }

        $consultation->update($validated);

        return back()->with('success', 'Consultation saved.');
    }

    public function complete(Consultation $consultation)
    {
        if ($consultation->status === 'completed') {
            return back()->with('error', 'Consultation already completed.');
        }

        DB::transaction(function () use ($consultation) {
            $consultation->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Sync appointment status
            if ($consultation->appointment_id) {
                $consultation->appointment->update(['status' => 'completed']);
            }

            // Sync queue status
            if ($consultation->walk_in_queue_id) {
                $consultation->walkInQueue->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            // Auto-log subscription usage if patient has active subscription with consultation item
            $subscription = PatientSubscription::where('patient_id', $consultation->patient_id)
                ->where('status', 'active')
                ->whereHas('package.items', fn($q) => $q->where('item_type', 'consultation'))
                ->first();

            if ($subscription) {
                $packageItem = $subscription->package->items()->where('item_type', 'consultation')->first();
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'appointment_id' => $consultation->appointment_id,
                    'consultation_id' => $consultation->id,
                    'package_item_id' => $packageItem?->id,
                    'item_type' => 'consultation',
                    'description' => 'Consultation by Dr. ' . ($consultation->doctor->user->name ?? 'Doctor'),
                    'quantity_used' => 1,
                    'used_at' => now(),
                    'recorded_by' => auth()->id(),
                ]);
                $subscription->increment('visits_used');
            }

            // Auto-log free consultation usage if patient has active membership with free consultations
            $membership = PatientMembership::where('patient_id', $consultation->patient_id)
                ->where('status', 'active')
                ->with('tier')
                ->first();

            if ($membership && $membership->tier->free_consultations_per_year > 0
                && $membership->free_consultations_used < $membership->tier->free_consultations_per_year) {
                $membership->increment('free_consultations_used');
                MembershipUsageLog::create([
                    'membership_id' => $membership->id,
                    'patient_id' => $consultation->patient_id,
                    'usage_type' => 'free_consultation',
                    'description' => 'Free consultation used (' . $membership->free_consultations_used . '/' . $membership->tier->free_consultations_per_year . ')',
                    'savings_amount' => $consultation->doctor->consultation_fee ?? 0,
                    'used_at' => now(),
                ]);
            }
        });

        // Redirect to invoice creation if no invoice yet
        if (!$consultation->invoice) {
            $params = ['consultation_id' => $consultation->id];
            if ($consultation->appointment_id) {
                $params['appointment_id'] = $consultation->appointment_id;
            }
            return redirect()->route('invoices.create', $params)
                ->with('success', 'Consultation completed. Create invoice now.');
        }

        return redirect()->route('consultations.show', $consultation)
            ->with('success', 'Consultation completed.');
    }

    public function show(Consultation $consultation)
    {
        $consultation->load([
            'patient', 'doctor.user', 'branch', 'appointment', 'walkInQueue',
            'prescriptions.items.medicine', 'labReports.items.labTest', 'invoice',
        ]);
        return view('consultations.show', compact('consultation'));
    }

    public function printMc(Consultation $consultation)
    {
        if (!$consultation->mc_issued) {
            abort(404);
        }
        $consultation->load(['patient', 'doctor.user', 'branch']);
        return view('consultations.mc-print', compact('consultation'));
    }

    public function destroy(Consultation $consultation)
    {
        if ($consultation->status === 'completed') {
            return back()->with('error', 'Cannot delete a completed consultation.');
        }

        $consultation->delete();
        return redirect()->route('consultations.index')->with('success', 'Consultation deleted.');
    }
}
