<?php

namespace App\Http\Controllers;

use App\Models\LocumDoctor;
use App\Models\LocumSession;
use App\Models\LocumPayment;
use App\Models\LocumInvitation;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\WalkInQueue;
use App\Models\TreatmentPlan;
use App\Models\TreatmentPlanSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LocumPortalController extends Controller
{
    public function login()
    {
        return view('locum-portal.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'ic_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $locum = LocumDoctor::where('ic_number', $request->ic_number)->first();

        if (!$locum || !$locum->password || !Hash::check($request->password, $locum->password)) {
            throw ValidationException::withMessages(['ic_number' => 'Invalid IC number or password.']);
        }

        if (!$locum->is_active) {
            throw ValidationException::withMessages(['ic_number' => 'Account is inactive.']);
        }

        session(['locum_id' => $locum->id]);
        $locum->update(['last_login_at' => now()]);

        return redirect()->route('locum-portal.dashboard');
    }

    public function logout()
    {
        session()->forget('locum_id');
        return redirect()->route('locum-portal.login')->with('success', 'Logged out.');
    }

    public function dashboard()
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) return redirect()->route('locum-portal.login');

        $totalSessions = LocumSession::where('locum_doctor_id', $locum->id)->count();
        $sessionsThisMonth = LocumSession::where('locum_doctor_id', $locum->id)
            ->whereMonth('session_date', now()->month)
            ->whereYear('session_date', now()->year)->count();
        $unpaidSessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('is_paid', false)->count();
        $unpaidAmount = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('is_paid', false)->sum('total_pay');
        $paidThisMonth = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('is_paid', true)
            ->whereMonth('session_date', now()->month)
            ->whereYear('session_date', now()->year)
            ->sum('total_pay');

        $upcomingSessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->limit(5)->get();

        $recentSessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->orderBy('session_date', 'desc')->limit(10)->get();

        $payments = LocumPayment::where('locum_doctor_id', $locum->id)
            ->orderBy('created_at', 'desc')->limit(5)->get();

        // Invitations
        $pendingInvitations = LocumInvitation::with('branch', 'createdBy')
            ->where('locum_doctor_id', $locum->id)
            ->where('status', 'pending')
            ->where('valid_to', '>', now())
            ->latest()->get();

        $activeInvitation = LocumInvitation::activeFor($locum->id);

        return view('locum-portal.dashboard', compact(
            'locum', 'totalSessions', 'sessionsThisMonth', 'unpaidSessions', 'unpaidAmount',
            'paidThisMonth', 'upcomingSessions', 'recentSessions', 'payments',
            'pendingInvitations', 'activeInvitation'
        ));
    }

    public function acceptInvitation(LocumInvitation $invitation)
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum || $invitation->locum_doctor_id !== $locum->id) abort(403);
        if ($invitation->status !== 'pending') return back()->with('error', 'Invitation no longer pending.');

        $invitation->update(['status' => 'accepted', 'accepted_at' => now()]);
        return back()->with('success', 'Invitation accepted. You can now access consultations during the period.');
    }

    public function declineInvitation(LocumInvitation $invitation)
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum || $invitation->locum_doctor_id !== $locum->id) abort(403);
        if ($invitation->status !== 'pending') return back()->with('error', 'Invitation no longer pending.');

        $invitation->update(['status' => 'declined']);
        return back()->with('success', 'Invitation declined.');
    }

    /* ============================ CONSULTATIONS ============================ */

    private function requireActive(string $permission)
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) abort(redirect()->route('locum-portal.login'));
        $invitation = LocumInvitation::activeFor($locum->id);
        if (!$invitation || !$invitation->{"can_{$permission}"}) {
            abort(403, 'You do not have an active invitation with this permission.');
        }
        return [$locum, $invitation];
    }

    public function consultations()
    {
        [$locum, $invitation] = $this->requireActive('consultation');

        // Patients in the queue at this branch right now (waiting + serving)
        $queueWaiting = WalkInQueue::where('branch_id', $invitation->branch_id)
            ->whereDate('queue_date', today())
            ->whereIn('status', ['waiting', 'serving'])
            ->orderByDesc('is_priority')
            ->orderBy('position')->get();

        // My consultations done during this invitation
        $myConsultations = Consultation::where('locum_doctor_id', $locum->id)
            ->where('locum_invitation_id', $invitation->id)
            ->with('patient')
            ->latest()->limit(20)->get();

        return view('locum-portal.consultations', compact('locum', 'invitation', 'queueWaiting', 'myConsultations'));
    }

    public function startConsultation(Request $request)
    {
        [$locum, $invitation] = $this->requireActive('consultation');

        $request->validate(['walk_in_queue_id' => 'required|exists:walk_in_queues,id']);
        $queue = WalkInQueue::findOrFail($request->walk_in_queue_id);

        if ($queue->branch_id !== $invitation->branch_id) abort(403);
        if (!$queue->patient_id) {
            return back()->with('error', 'This queue entry has no linked patient. Reception must register them first.');
        }

        // Reuse if already started
        $existing = Consultation::where('walk_in_queue_id', $queue->id)
            ->where('status', 'in_progress')->first();
        if ($existing) {
            return redirect()->route('locum-portal.consultations.edit', $existing);
        }

        $branch = $invitation->branch;
        $consultation = Consultation::create([
            'consultation_number' => Consultation::generateNumber($branch->code ?? 'BR'),
            'branch_id' => $branch->id,
            'patient_id' => $queue->patient_id,
            'doctor_id' => $queue->doctor_id ?: \App\Models\Doctor::where('branch_id', $branch->id)->first()?->id ?: 1,
            'walk_in_queue_id' => $queue->id,
            'locum_doctor_id' => $locum->id,
            'locum_invitation_id' => $invitation->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Mark queue as serving
        $queue->update(['status' => 'serving', 'called_at' => now()]);

        return redirect()->route('locum-portal.consultations.edit', $consultation);
    }

    /**
     * Locum can edit a consultation if:
     * 1. They own it (locum_doctor_id matches), OR
     * 2. The consultation is at their active invitation's branch AND has no
     *    other locum claim — auto-claim it so legacy/orphaned ones work.
     */
    private function canEditConsultation(Consultation $consultation, ?LocumDoctor $locum): bool
    {
        if (!$locum) return false;
        if ($consultation->locum_doctor_id === $locum->id) return true;

        if ($consultation->locum_doctor_id === null) {
            $invitation = LocumInvitation::activeFor($locum->id);
            if ($invitation && $consultation->branch_id === $invitation->branch_id) {
                // Auto-claim
                $consultation->update([
                    'locum_doctor_id' => $locum->id,
                    'locum_invitation_id' => $invitation->id,
                ]);
                return true;
            }
        }
        return false;
    }

    public function editConsultation(Consultation $consultation)
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$this->canEditConsultation($consultation, $locum)) abort(403);
        $consultation->load(['patient', 'walkInQueue']);
        return view('locum-portal.consultation-edit', compact('locum', 'consultation'));
    }

    public function updateConsultation(Request $request, Consultation $consultation)
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$this->canEditConsultation($consultation, $locum)) abort(403);

        $validated = $request->validate([
            'bp_systolic' => 'nullable|numeric',
            'bp_diastolic' => 'nullable|numeric',
            'pulse' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'weight_kg' => 'nullable|numeric',
            'height_cm' => 'nullable|numeric',
            'spo2' => 'nullable|numeric',
            'chief_complaint' => 'nullable|string',
            'history' => 'nullable|string',
            'examination' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
        ]);

        // Auto-calc BMI
        if (!empty($validated['weight_kg']) && !empty($validated['height_cm']) && $validated['height_cm'] > 0) {
            $hM = $validated['height_cm'] / 100;
            $validated['bmi'] = round($validated['weight_kg'] / ($hM * $hM), 2);
        }

        $consultation->update($validated);
        return back()->with('success', 'Consultation saved.');
    }

    public function completeConsultation(Consultation $consultation)
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$this->canEditConsultation($consultation, $locum)) abort(403);

        DB::transaction(function () use ($consultation) {
            $consultation->update(['status' => 'completed', 'completed_at' => now()]);
            if ($consultation->walk_in_queue_id) {
                $consultation->walkInQueue->update(['status' => 'completed', 'completed_at' => now()]);
            }
        });

        return redirect()->route('locum-portal.consultations')->with('success', "Consultation {$consultation->consultation_number} completed. Reception will handle billing.");
    }

    /* ============================ TREATMENT PLANS ============================ */

    public function treatmentPlans()
    {
        [$locum, $invitation] = $this->requireActive('treatment_plan');

        $plans = TreatmentPlan::where('created_by_locum_id', $locum->id)
            ->with(['patient', 'sessions'])
            ->latest()->paginate(15);

        return view('locum-portal.treatment-plans', compact('locum', 'invitation', 'plans'));
    }

    public function createTreatmentPlan()
    {
        [$locum, $invitation] = $this->requireActive('treatment_plan');
        $patients = Patient::where('branch_id', $invitation->branch_id)
            ->where('is_active', true)->orderBy('name')->get();
        return view('locum-portal.treatment-plan-create', compact('locum', 'invitation', 'patients'));
    }

    public function storeTreatmentPlan(Request $request)
    {
        [$locum, $invitation] = $this->requireActive('treatment_plan');

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'title' => 'required|string|max:255',
            'diagnosis' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'total_sessions' => 'required|integer|min:1',
            'interval_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $locum, $invitation) {
            $startDate = \Carbon\Carbon::parse($validated['start_date']);
            $expectedEnd = $startDate->copy()->addDays(($validated['total_sessions'] - 1) * $validated['interval_days']);

            // Locum doesn't have a Doctor row, so we link to a placeholder doctor at the branch
            $placeholderDoctor = \App\Models\Doctor::where('branch_id', $invitation->branch_id)->first()
                ?? \App\Models\Doctor::first();

            $plan = TreatmentPlan::create([
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $placeholderDoctor->id ?? 1,
                'created_by_locum_id' => $locum->id,
                'branch_id' => $invitation->branch_id,
                'plan_number' => TreatmentPlan::generateNumber(),
                'title' => $validated['title'],
                'diagnosis' => $validated['diagnosis'] ?? null,
                'description' => $validated['description'] ?? null,
                'total_sessions' => $validated['total_sessions'],
                'status' => 'active',
                'approval_status' => $invitation->treatment_plan_requires_approval ? 'pending_approval' : 'auto_approved',
                'start_date' => $startDate,
                'expected_end_date' => $expectedEnd,
                'notes' => $validated['notes'] ?? null,
            ]);

            for ($i = 1; $i <= $validated['total_sessions']; $i++) {
                TreatmentPlanSession::create([
                    'treatment_plan_id' => $plan->id,
                    'session_number' => $i,
                    'scheduled_date' => $startDate->copy()->addDays(($i - 1) * $validated['interval_days']),
                    'status' => 'pending',
                ]);
            }
        });

        $msg = $invitation->treatment_plan_requires_approval
            ? 'Treatment plan submitted. Pending admin approval.'
            : 'Treatment plan created.';
        return redirect()->route('locum-portal.treatment-plans')->with('success', $msg);
    }

    public function sessions()
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) return redirect()->route('locum-portal.login');

        $sessions = LocumSession::where('locum_doctor_id', $locum->id)
            ->orderBy('session_date', 'desc')->paginate(15);

        return view('locum-portal.sessions', compact('locum', 'sessions'));
    }

    public function payments()
    {
        $locum = LocumDoctor::find(session('locum_id'));
        if (!$locum) return redirect()->route('locum-portal.login');

        $payments = LocumPayment::where('locum_doctor_id', $locum->id)
            ->with('items.locumSession')
            ->orderBy('created_at', 'desc')->paginate(15);

        return view('locum-portal.payments', compact('locum', 'payments'));
    }
}
