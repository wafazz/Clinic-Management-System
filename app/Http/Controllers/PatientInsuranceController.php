<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientInsurance;
use App\Models\InsurancePanel;
use Illuminate\Http\Request;

class PatientInsuranceController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $branchId = session('current_branch_id');

        $validated = $request->validate([
            'insurance_panel_id' => 'required|exists:insurance_panels,id',
            'member_id' => 'nullable|string|max:100',
            'policy_number' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:effective_date',
            'remaining_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['patient_id'] = $patient->id;
        $validated['status'] = 'active';

        PatientInsurance::create($validated);

        return back()->with('success', 'Insurance coverage added for ' . $patient->name);
    }

    public function update(Request $request, PatientInsurance $patientInsurance)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|string|max:100',
            'policy_number' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'effective_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'remaining_limit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,expired,suspended',
            'notes' => 'nullable|string|max:500',
        ]);

        $patientInsurance->update($validated);

        return back()->with('success', 'Insurance coverage updated.');
    }

    public function destroy(PatientInsurance $patientInsurance)
    {
        if ($patientInsurance->claims()->exists()) {
            return back()->with('error', 'Cannot remove coverage with existing claims.');
        }

        $patient = $patientInsurance->patient;
        $patientInsurance->delete();

        return back()->with('success', 'Insurance coverage removed.');
    }
}
