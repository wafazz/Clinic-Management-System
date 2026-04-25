<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $appointments = Appointment::with(['patient', 'doctor.user', 'branch', 'queueEntry'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->date, fn($q, $date) => $q->whereDate('appointment_date', $date))
            ->when($request->doctor_id, fn($q, $id) => $q->where('doctor_id', $id))
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(15)
            ->withQueryString();

        $doctors = Doctor::with('user')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->get();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    public function create(Request $request)
    {
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::with(['user', 'schedules'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->get();

        $selectedPatient = $request->patient_id;
        $selectedDoctor = $request->doctor_id;

        // Build doctor data map for live JS — name, fee, specialization, schedule
        $doctorMap = $doctors->mapWithKeys(fn($d) => [
            $d->id => [
                'name' => $d->user->name,
                'specialization' => $d->specialization ?? 'General Practice',
                'fee' => (float) ($d->consultation_fee ?? 0),
                'mmc' => $d->mmc_number,
                'schedule' => $d->schedules->mapWithKeys(fn($s) => [
                    strtolower($s->day_of_week) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5),
                ])->all(),
            ],
        ])->all();

        // Patient data for live preview
        $patientMap = $patients->mapWithKeys(fn($p) => [
            $p->id => [
                'name' => $p->name,
                'patient_id' => $p->patient_id,
                'phone' => $p->phone,
                'allergies' => $p->allergies,
                'age' => $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age : null,
            ],
        ])->all();

        return view('appointments.create', compact('patients', 'doctors', 'selectedPatient', 'selectedDoctor', 'doctorMap', 'patientMap'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $doctor = Doctor::findOrFail($validated['doctor_id']);
        $validated['branch_id'] = $doctor->branch_id;
        $validated['status'] = 'pending';

        Appointment::create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor.user', 'branch', 'invoice', 'queueEntry']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::with(['user', 'schedules'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->get();

        $doctorMap = $doctors->mapWithKeys(fn($d) => [
            $d->id => [
                'name' => $d->user->name,
                'specialization' => $d->specialization ?? 'General Practice',
                'fee' => (float) ($d->consultation_fee ?? 0),
                'mmc' => $d->mmc_number,
                'schedule' => $d->schedules->mapWithKeys(fn($s) => [
                    strtolower($s->day_of_week) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5),
                ])->all(),
            ],
        ])->all();

        $patientMap = $patients->mapWithKeys(fn($p) => [
            $p->id => [
                'name' => $p->name,
                'patient_id' => $p->patient_id,
                'phone' => $p->phone,
                'allergies' => $p->allergies,
                'age' => $p->date_of_birth ? \Carbon\Carbon::parse($p->date_of_birth)->age : null,
            ],
        ])->all();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'doctorMap', 'patientMap'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);
        return redirect()->route('appointments.index')->with('success', 'Appointment updated.');
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,in_progress,completed,cancelled,no_show']);
        $appointment->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Status updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}
