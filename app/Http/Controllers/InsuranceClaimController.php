<?php

namespace App\Http\Controllers;

use App\Models\InsuranceClaim;
use App\Models\InsurancePanel;
use App\Models\Invoice;
use App\Models\Branch;
use Illuminate\Http\Request;

class InsuranceClaimController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $claims = InsuranceClaim::where('branch_id', $branchId)
            ->with(['patient', 'panel', 'invoice'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('panel_id'), fn($q) => $q->where('insurance_panel_id', $request->panel_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('claim_number', 'like', "%{$request->search}%")
                       ->orWhere('gl_number', 'like', "%{$request->search}%")
                       ->orWhereHas('patient', fn($q3) => $q3->where('name', 'like', "%{$request->search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $panels = InsurancePanel::where('branch_id', $branchId)->where('is_active', true)->orderBy('company_name')->get();

        return view('insurance.claims.index', compact('claims', 'panels'));
    }

    public function create(Request $request)
    {
        $branchId = session('current_branch_id');

        // Only show panel invoices without claims yet
        $invoices = Invoice::where('branch_id', $branchId)
            ->where('payment_type', 'panel')
            ->whereDoesntHave('insuranceClaim')
            ->with(['patient', 'insurancePanel'])
            ->latest()
            ->get();

        $selectedInvoice = $request->filled('invoice_id') ? $request->invoice_id : null;

        return view('insurance.claims.create', compact('invoices', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $branchId = session('current_branch_id');
        $branch = Branch::findOrFail($branchId);

        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'claim_amount' => 'required|numeric|min:0.01',
            'patient_copay' => 'nullable|numeric|min:0',
            'gl_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::with(['patient', 'insurancePanel', 'patientInsurance'])->findOrFail($request->invoice_id);

        if (!$invoice->isPanelInvoice()) {
            return back()->with('error', 'This invoice is not a panel invoice.');
        }

        $glStatus = 'not_required';
        if ($invoice->insurancePanel && $invoice->insurancePanel->requires_gl) {
            $glStatus = $request->gl_number ? 'approved' : 'pending';
        }

        InsuranceClaim::create([
            'branch_id' => $branchId,
            'invoice_id' => $invoice->id,
            'patient_id' => $invoice->patient_id,
            'insurance_panel_id' => $invoice->insurance_panel_id,
            'patient_insurance_id' => $invoice->patient_insurance_id,
            'claim_number' => InsuranceClaim::generateClaimNumber($branch->code),
            'gl_number' => $request->gl_number,
            'gl_status' => $glStatus,
            'claim_amount' => $request->claim_amount,
            'patient_copay' => $request->patient_copay ?? 0,
            'status' => 'draft',
            'notes' => $request->notes,
        ]);

        return redirect()->route('insurance-claims.index')->with('success', 'Insurance claim created.');
    }

    public function show(InsuranceClaim $insuranceClaim)
    {
        $insuranceClaim->load(['patient', 'panel', 'invoice.items', 'invoice.payments', 'patientInsurance']);
        return view('insurance.claims.show', compact('insuranceClaim'));
    }

    public function updateStatus(Request $request, InsuranceClaim $insuranceClaim)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,approved,partial,rejected,paid',
            'approved_amount' => 'nullable|numeric|min:0',
            'rejection_reason' => 'nullable|string|max:500',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'submitted') {
            $data['submission_date'] = now();
        } elseif (in_array($request->status, ['approved', 'partial'])) {
            $data['approved_amount'] = $request->approved_amount ?? $insuranceClaim->claim_amount;
            $data['approval_date'] = now();
        } elseif ($request->status === 'rejected') {
            $data['rejection_reason'] = $request->rejection_reason;
        } elseif ($request->status === 'paid') {
            $data['payment_date'] = now();
            $data['payment_reference'] = $request->payment_reference;
        }

        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        $insuranceClaim->update($data);

        return back()->with('success', 'Claim status updated to ' . ucfirst($request->status) . '.');
    }

    public function destroy(InsuranceClaim $insuranceClaim)
    {
        if (in_array($insuranceClaim->status, ['approved', 'paid'])) {
            return back()->with('error', 'Cannot delete approved or paid claims.');
        }

        $insuranceClaim->delete();
        return redirect()->route('insurance-claims.index')->with('success', 'Claim deleted.');
    }
}
