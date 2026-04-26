<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Branch;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $referrals = Referral::with(['patient', 'referringDoctor.user'])
            ->where('branch_id', $branchId)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('referrals.index', compact('referrals'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->with('user')->get();
        return view('referrals.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'referring_doctor_id' => 'nullable|exists:doctors,id',
            'referred_to' => 'required|string|max:255',
            'specialty' => 'nullable|string|max:255',
            'reason' => 'required|string',
            'clinical_summary' => 'nullable|string',
            'referral_date' => 'required|date',
            'urgency' => 'required|in:routine,urgent,emergency',
            'notes' => 'nullable|string',
        ]);

        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);
        $validated['branch_id'] = $branchId;
        $validated['referral_number'] = Referral::generateNumber($branch->code ?? 'BR');
        $validated['status'] = 'pending';

        Referral::create($validated);
        return redirect()->route('referrals.index')->with('success', 'Referral created.');
    }

    public function show(Referral $referral)
    {
        $referral->load(['patient', 'referringDoctor.user', 'branch', 'consultation']);
        return view('referrals.show', compact('referral'));
    }

    public function updateStatus(Request $request, Referral $referral)
    {
        $request->validate(['status' => 'required|in:pending,sent,completed,cancelled']);
        $referral->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }

    public function destroy(Referral $referral)
    {
        $referral->delete();
        return redirect()->route('referrals.index')->with('success', 'Referral deleted.');
    }
}
