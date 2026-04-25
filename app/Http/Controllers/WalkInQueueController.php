<?php

namespace App\Http\Controllers;

use App\Models\WalkInQueue;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;

class WalkInQueueController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $date = $request->date ?? now()->toDateString();

        $queues = WalkInQueue::with(['patient', 'doctor.user', 'branch', 'appointment'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', $date)
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'serving', 'waiting', 'completed', 'skipped', 'cancelled')")
            ->orderBy('position', 'asc')
            ->get();

        $stats = [
            'total' => $queues->count(),
            'waiting' => $queues->where('status', 'waiting')->count(),
            'serving' => $queues->where('status', 'serving')->count(),
            'completed' => $queues->where('status', 'completed')->count(),
            'skipped' => $queues->where('status', 'skipped')->count(),
        ];

        $currentServing = $queues->where('status', 'serving')->first();

        return view('walk-in-queue.index', compact('queues', 'stats', 'currentServing', 'date'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::with('user')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->get();

        return view('walk-in-queue.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'nullable|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'nullable|string|max:20',
            'reason' => 'nullable|string|max:255',
        ]);

        $branchId = session('current_branch_id');
        if (!$branchId) {
            return redirect()->back()->with('error', 'Please select a branch first.');
        }

        $today = now()->toDateString();
        $position = WalkInQueue::getNextPosition($branchId, $today);
        $queueNumber = WalkInQueue::generateQueueNumber($branchId, $today);

        WalkInQueue::create([
            'branch_id' => $branchId,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'patient_name' => $validated['patient_name'],
            'patient_phone' => $validated['patient_phone'],
            'queue_number' => $queueNumber,
            'queue_date' => $today,
            'reason' => $validated['reason'],
            'status' => 'waiting',
            'position' => $position,
        ]);

        return redirect()->route('walk-in-queue.index')->with('success', "Patient added to queue. Nombor Giliran: {$queueNumber}");
    }

    public function updateStatus(Request $request, WalkInQueue $walkInQueue)
    {
        $request->validate(['status' => 'required|in:waiting,serving,completed,skipped,cancelled']);

        $newStatus = $request->status;
        $updates = ['status' => $newStatus];

        if ($newStatus === 'serving') {
            $updates['called_at'] = now();
        } elseif (in_array($newStatus, ['completed', 'skipped', 'cancelled'])) {
            $updates['completed_at'] = now();
        }

        $walkInQueue->update($updates);

        // Sync appointment status
        if ($walkInQueue->appointment_id) {
            $appointmentStatus = match($newStatus) {
                'serving' => 'in_progress',
                'completed' => 'completed',
                'cancelled' => 'cancelled',
                'skipped' => 'no_show',
                default => null,
            };
            if ($appointmentStatus) {
                $walkInQueue->appointment->update(['status' => $appointmentStatus]);
            }
        }

        return redirect()->back()->with('success', "Queue {$walkInQueue->queue_number} status updated to {$newStatus}.");
    }

    public function checkIn(Appointment $appointment)
    {
        // Prevent duplicate check-in
        $existing = WalkInQueue::where('appointment_id', $appointment->id)
            ->whereDate('queue_date', today())
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existing) {
            return redirect()->route('walk-in-queue.index')->with('error', "Patient already checked in. Nombor Giliran: {$existing->queue_number}");
        }

        $branchId = $appointment->branch_id;
        $today = now()->toDateString();
        $position = WalkInQueue::getNextPosition($branchId, $today);
        $queueNumber = WalkInQueue::generateQueueNumber($branchId, $today, 'appointment');

        WalkInQueue::create([
            'branch_id' => $branchId,
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'queue_number' => $queueNumber,
            'type' => 'appointment',
            'patient_name' => $appointment->patient->name,
            'patient_phone' => $appointment->patient->phone,
            'queue_date' => $today,
            'reason' => $appointment->reason,
            'status' => 'waiting',
            'position' => $position,
        ]);

        // Update appointment to confirmed
        if ($appointment->status === 'pending') {
            $appointment->update(['status' => 'confirmed']);
        }

        return redirect()->route('walk-in-queue.index')->with('success', "Patient checked in. Nombor Giliran: {$queueNumber}");
    }

    public function callNext(Request $request)
    {
        $branchId = session('current_branch_id');
        $today = now()->toDateString();

        $next = WalkInQueue::where('branch_id', $branchId)
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderBy('position', 'asc')
            ->first();

        if (!$next) {
            return redirect()->back()->with('error', 'No more patients in queue.');
        }

        $next->update([
            'status' => 'serving',
            'called_at' => now(),
        ]);

        // Sync appointment status
        if ($next->appointment_id) {
            $next->appointment->update(['status' => 'in_progress']);
        }

        return redirect()->back()->with('success', "Calling {$next->queue_number} - {$next->patient_name}");
    }

    public function display()
    {
        $branchId = session('current_branch_id');
        $today = now()->toDateString();

        $currentServing = WalkInQueue::with(['doctor.user'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', $today)
            ->where('status', 'serving')
            ->orderBy('called_at', 'desc')
            ->get();

        $waiting = WalkInQueue::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderBy('position', 'asc')
            ->get();

        $branchName = '';
        if ($branchId) {
            $branchName = \App\Models\Branch::find($branchId)->name ?? '';
        }

        return view('walk-in-queue.display', compact('currentServing', 'waiting', 'branchName', 'today'));
    }

    public function displayData(Request $request)
    {
        $branchId = session('current_branch_id');
        $today = now()->toDateString();

        $currentServing = WalkInQueue::with(['doctor.user'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', $today)
            ->where('status', 'serving')
            ->orderBy('called_at', 'desc')
            ->get()
            ->map(fn($q) => [
                'queue_number' => $q->queue_number,
                'patient_name' => $q->patient_name,
                'doctor_name' => $q->doctor ? 'Dr. ' . $q->doctor->user->name : '-',
            ]);

        $waiting = WalkInQueue::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDate('queue_date', $today)
            ->where('status', 'waiting')
            ->orderBy('position', 'asc')
            ->get()
            ->map(fn($q) => [
                'queue_number' => $q->queue_number,
                'patient_name' => $q->patient_name,
            ]);

        return response()->json([
            'current_serving' => $currentServing,
            'waiting' => $waiting,
        ]);
    }

    public function destroy(WalkInQueue $walkInQueue)
    {
        $walkInQueue->delete();
        return redirect()->route('walk-in-queue.index')->with('success', 'Queue entry deleted.');
    }
}
