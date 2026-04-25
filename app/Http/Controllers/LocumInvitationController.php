<?php

namespace App\Http\Controllers;

use App\Models\LocumInvitation;
use App\Models\LocumDoctor;
use App\Models\Branch;
use Illuminate\Http\Request;

class LocumInvitationController extends Controller
{
    public function index(Request $request)
    {
        $invitations = LocumInvitation::with(['locumDoctor', 'branch', 'createdBy'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->locum_doctor_id, fn($q, $id) => $q->where('locum_doctor_id', $id))
            ->latest()->paginate(15)->withQueryString();

        // Mark expired invitations on read (cheap)
        LocumInvitation::where('status', 'pending')
            ->where('valid_to', '<', now())
            ->update(['status' => 'expired']);

        $locumDoctors = LocumDoctor::where('is_active', true)->orderBy('name')->get();

        return view('locum-invitations.index', compact('invitations', 'locumDoctors'));
    }

    public function create()
    {
        $locumDoctors = LocumDoctor::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();
        return view('locum-invitations.create', compact('locumDoctors', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'locum_doctor_id' => 'required|exists:locum_doctors,id',
            'branch_id' => 'required|exists:branches,id',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date|after:valid_from',
            'can_consultation' => 'nullable|boolean',
            'can_treatment_plan' => 'nullable|boolean',
            'treatment_plan_requires_approval' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['can_consultation'] = $request->boolean('can_consultation');
        $validated['can_treatment_plan'] = $request->boolean('can_treatment_plan');
        $validated['treatment_plan_requires_approval'] = $request->boolean('treatment_plan_requires_approval', true);
        $validated['status'] = 'pending';
        $validated['created_by'] = auth()->id();

        LocumInvitation::create($validated);

        return redirect()->route('locum-invitations.index')->with('success', 'Invitation created. Locum will see it in their portal.');
    }

    public function show(LocumInvitation $locumInvitation)
    {
        $locumInvitation->load(['locumDoctor', 'branch', 'createdBy']);
        return view('locum-invitations.show', compact('locumInvitation'));
    }

    public function revoke(LocumInvitation $locumInvitation)
    {
        $locumInvitation->update(['status' => 'revoked', 'revoked_at' => now()]);
        return back()->with('success', 'Invitation revoked.');
    }

    public function destroy(LocumInvitation $locumInvitation)
    {
        if ($locumInvitation->status === 'accepted' && $locumInvitation->isActive()) {
            return back()->with('error', 'Cannot delete an active invitation. Revoke it first.');
        }
        $locumInvitation->delete();
        return redirect()->route('locum-invitations.index')->with('success', 'Invitation deleted.');
    }
}
