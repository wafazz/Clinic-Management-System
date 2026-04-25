<?php

namespace App\Http\Controllers;

use App\Models\PatientSubscription;
use App\Models\ServicePackage;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $subscriptions = PatientSubscription::with(['patient', 'package'])
            ->where('branch_id', $branchId)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('patient-subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $patients = Patient::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();
        $packages = ServicePackage::where('is_active', true)->get();
        return view('patient-subscriptions.create', compact('patients', 'packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'package_id' => 'required|exists:service_packages,id',
            'payment_mode' => 'required|in:full,partial',
            'deposit_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,online',
        ]);

        $package = ServicePackage::find($validated['package_id']);
        $branchId = session('current_branch_id');

        $deposit = $validated['payment_mode'] === 'full' ? $package->price : ($validated['deposit_amount'] ?? 0);
        $balance = $package->price - $deposit;
        $perSession = $package->max_visits && $balance > 0 ? round($balance / $package->max_visits, 2) : 0;

        PatientSubscription::create([
            'patient_id' => $validated['patient_id'],
            'package_id' => $validated['package_id'],
            'branch_id' => $branchId,
            'subscription_number' => PatientSubscription::generateNumber(),
            'status' => 'active',
            'payment_mode' => $validated['payment_mode'],
            'total_amount' => $package->price,
            'deposit_amount' => $deposit,
            'balance_amount' => $balance,
            'per_session_amount' => $perSession,
            'total_paid' => $deposit,
            'start_date' => $validated['start_date'],
            'end_date' => $package->duration_days ? now()->parse($validated['start_date'])->addDays($package->duration_days) : null,
            'visits_total' => $package->max_visits,
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('patient-subscriptions.index')->with('success', 'Subscription created.');
    }

    public function show(PatientSubscription $patientSubscription)
    {
        $patientSubscription->load(['patient', 'package.items', 'payments', 'usages']);
        return view('patient-subscriptions.show', compact('patientSubscription'));
    }

    public function destroy(PatientSubscription $patientSubscription)
    {
        $patientSubscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
        return redirect()->route('patient-subscriptions.index')->with('success', 'Subscription cancelled.');
    }
}
