<?php

namespace App\Http\Controllers;

use App\Models\LocumSession;
use App\Models\LocumDoctor;
use App\Models\Branch;
use Illuminate\Http\Request;

class LocumSessionController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $locumSessions = LocumSession::with(['locumDoctor', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->date, fn($q, $d) => $q->whereDate('session_date', $d))
            ->orderBy('session_date', 'desc')
            ->paginate(15)->withQueryString();

        return view('locum-sessions.index', compact('locumSessions'));
    }

    public function create()
    {
        $locumDoctors = LocumDoctor::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        return view('locum-sessions.create', compact('locumDoctors', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'locum_doctor_id' => 'required|exists:locum_doctors,id',
            'branch_id' => 'required|exists:branches,id',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $locum = LocumDoctor::findOrFail($validated['locum_doctor_id']);

        // Calculate total pay
        $start = strtotime($validated['start_time']);
        $end = strtotime($validated['end_time']);
        $hours = ($end - $start) / 3600;

        if ($locum->session_rate > 0) {
            $validated['total_pay'] = $locum->session_rate;
        } else {
            $validated['total_pay'] = round($hours * $locum->hourly_rate, 2);
        }

        $validated['status'] = 'scheduled';
        $validated['is_paid'] = false;

        LocumSession::create($validated);

        return redirect()->route('locum-sessions.index')->with('success', 'Session scheduled.');
    }

    public function show(LocumSession $locumSession)
    {
        $locumSession->load(['locumDoctor', 'branch']);

        // If linked to an invitation, surface its details + count consultations done
        $invitation = $locumSession->locum_invitation_id
            ? \App\Models\LocumInvitation::with('createdBy')->find($locumSession->locum_invitation_id)
            : null;

        $consultationsCount = 0;
        $consultations = collect();
        if ($invitation) {
            $consultations = \App\Models\Consultation::where('locum_invitation_id', $invitation->id)
                ->with('patient')->latest()->limit(20)->get();
            $consultationsCount = \App\Models\Consultation::where('locum_invitation_id', $invitation->id)->count();
        }

        return view('locum-sessions.show', compact('locumSession', 'invitation', 'consultations', 'consultationsCount'));
    }

    public function edit(LocumSession $locumSession)
    {
        $locumDoctors = LocumDoctor::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        return view('locum-sessions.edit', compact('locumSession', 'locumDoctors', 'branches'));
    }

    public function update(Request $request, LocumSession $locumSession)
    {
        $validated = $request->validate([
            'locum_doctor_id' => 'required|exists:locum_doctors,id',
            'branch_id' => 'required|exists:branches,id',
            'session_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $locum = LocumDoctor::findOrFail($validated['locum_doctor_id']);
        $start = strtotime($validated['start_time']);
        $end = strtotime($validated['end_time']);
        $hours = ($end - $start) / 3600;

        if ($locum->session_rate > 0) {
            $validated['total_pay'] = $locum->session_rate;
        } else {
            $validated['total_pay'] = round($hours * $locum->hourly_rate, 2);
        }

        $locumSession->update($validated);

        return redirect()->route('locum-sessions.index')->with('success', 'Session updated.');
    }

    public function markPaid(LocumSession $locumSession)
    {
        $locumSession->update(['is_paid' => true]);
        return redirect()->back()->with('success', 'Session marked as paid.');
    }

    public function destroy(LocumSession $locumSession)
    {
        $locumSession->delete();
        return redirect()->route('locum-sessions.index')->with('success', 'Session deleted.');
    }
}
