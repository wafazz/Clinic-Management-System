<?php

namespace App\Http\Controllers;

use App\Models\PatientMembership;
use App\Models\MembershipTier;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientMembershipController extends Controller
{
    public function index(Request $request)
    {
        $memberships = PatientMembership::with(['patient', 'tier'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('patient', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            })
            ->latest()->paginate(15)->withQueryString();
        return view('patient-memberships.index', compact('memberships'));
    }

    public function create()
    {
        $patients = Patient::where('is_active', true)->orderBy('name')->get();
        $tiers = MembershipTier::where('is_active', true)->orderBy('sort_order')->get();
        return view('patient-memberships.create', compact('patients', 'tiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'tier_id' => 'required|exists:membership_tiers,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'auto_renew' => 'nullable|boolean',
            'payment_method' => 'nullable|in:cash,card,online',
        ]);

        $tier = MembershipTier::find($validated['tier_id']);
        if (!$validated['end_date']) {
            $validated['end_date'] = match ($tier->billing_cycle) {
                'monthly' => now()->parse($validated['start_date'])->addMonth(),
                'yearly' => now()->parse($validated['start_date'])->addYear(),
                default => null,
            };
        }

        $validated['membership_number'] = PatientMembership::generateNumber();
        $validated['status'] = 'active';
        $validated['auto_renew'] = $request->boolean('auto_renew');

        PatientMembership::create($validated);
        return redirect()->route('patient-memberships.index')->with('success', 'Membership created.');
    }

    public function show(PatientMembership $patientMembership)
    {
        $patientMembership->load(['patient', 'tier', 'familyMembers.patient', 'usageLogs']);
        return view('patient-memberships.show', compact('patientMembership'));
    }

    public function destroy(PatientMembership $patientMembership)
    {
        $patientMembership->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        return redirect()->route('patient-memberships.index')->with('success', 'Membership cancelled.');
    }
}
