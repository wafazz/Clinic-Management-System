<?php

namespace App\Http\Controllers;

use App\Models\InsurancePanel;
use Illuminate\Http\Request;

class InsurancePanelController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');

        $panels = InsurancePanel::where('branch_id', $branchId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('company_name', 'like', "%{$search}%")
                       ->orWhere('type', 'like', "%{$search}%")
                       ->orWhere('contact_person', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->withCount('patientInsurances')
            ->orderBy('company_name')
            ->paginate(15)
            ->withQueryString();

        return view('insurance.panels.index', compact('panels', 'search'));
    }

    public function create()
    {
        return view('insurance.panels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'type' => 'required|in:corporate,insurance,tpa,government',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'credit_terms' => 'required|integer|min:0|max:365',
            'consultation_limit' => 'nullable|numeric|min:0',
            'annual_limit' => 'nullable|numeric|min:0',
            'covered_services' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'notes' => 'nullable|string',
            'requires_gl' => 'boolean',
        ]);

        $validated['branch_id'] = session('current_branch_id');
        $validated['requires_gl'] = $request->boolean('requires_gl');
        $validated['is_active'] = $request->boolean('is_active', true);

        InsurancePanel::create($validated);

        return redirect()->route('insurance-panels.index')->with('success', 'Insurance panel created successfully.');
    }

    public function show(InsurancePanel $insurancePanel)
    {
        $insurancePanel->load(['patientInsurances.patient', 'claims' => fn($q) => $q->latest()->limit(20)]);

        $totalClaims = $insurancePanel->claims()->count();
        $totalClaimAmount = $insurancePanel->claims()->sum('claim_amount');
        $pendingClaims = $insurancePanel->claims()->whereIn('status', ['draft', 'submitted'])->count();
        $unpaidAmount = $insurancePanel->claims()->whereIn('status', ['approved', 'partial'])->sum('approved_amount');

        return view('insurance.panels.show', compact('insurancePanel', 'totalClaims', 'totalClaimAmount', 'pendingClaims', 'unpaidAmount'));
    }

    public function edit(InsurancePanel $insurancePanel)
    {
        return view('insurance.panels.edit', compact('insurancePanel'));
    }

    public function update(Request $request, InsurancePanel $insurancePanel)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'type' => 'required|in:corporate,insurance,tpa,government',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'credit_terms' => 'required|integer|min:0|max:365',
            'consultation_limit' => 'nullable|numeric|min:0',
            'annual_limit' => 'nullable|numeric|min:0',
            'covered_services' => 'nullable|string',
            'exclusions' => 'nullable|string',
            'notes' => 'nullable|string',
            'requires_gl' => 'boolean',
        ]);

        $validated['requires_gl'] = $request->boolean('requires_gl');
        $validated['is_active'] = $request->boolean('is_active');
        $insurancePanel->update($validated);

        return redirect()->route('insurance-panels.index')->with('success', 'Insurance panel updated successfully.');
    }

    public function destroy(InsurancePanel $insurancePanel)
    {
        if ($insurancePanel->claims()->exists()) {
            return back()->with('error', 'Cannot delete panel with existing claims.');
        }

        $insurancePanel->delete();
        return redirect()->route('insurance-panels.index')->with('success', 'Insurance panel deleted successfully.');
    }
}
