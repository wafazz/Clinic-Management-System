<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Service;
use App\Models\Branch;
use App\Models\InsurancePanel;
use App\Models\PatientInsurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $invoices = Invoice::with(['patient', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('created_at', 'desc')
            ->paginate(15)->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $services = Service::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $appointments = Appointment::with('patient')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereDoesntHave('invoice')
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')->get();

        $insurancePanels = InsurancePanel::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('company_name')->get();
        $patientInsurances = PatientInsurance::with('panel')
            ->where('status', 'active')->get();

        $selectedAppointment = $request->appointment_id;
        $selectedConsultation = null;
        $selectedPatient = null;
        if ($request->filled('consultation_id')) {
            $selectedConsultation = Consultation::with('patient', 'doctor.user')->find($request->consultation_id);
            if ($selectedConsultation) {
                $selectedPatient = $selectedConsultation->patient_id;
                if (!$selectedAppointment && $selectedConsultation->appointment_id) {
                    $selectedAppointment = $selectedConsultation->appointment_id;
                }
            }
        }
        return view('invoices.create', compact('patients', 'services', 'appointments', 'selectedAppointment', 'selectedConsultation', 'selectedPatient', 'insurancePanels', 'patientInsurances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'payment_type' => 'nullable|in:cash,panel',
            'insurance_panel_id' => 'nullable|exists:insurance_panels,id',
            'patient_insurance_id' => 'nullable|exists:patient_insurances,id',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);
        $branch = $patient->branch;

        DB::transaction(function () use ($validated, $branch, $patient) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = $validated['tax'] ?? 0;
            $discount = $validated['discount'] ?? 0;
            $total = $subtotal + $tax - $discount;

            $invoice = Invoice::create([
                'branch_id' => $branch->id,
                'patient_id' => $patient->id,
                'appointment_id' => $validated['appointment_id'] ?? null,
                'consultation_id' => $validated['consultation_id'] ?? null,
                'invoice_number' => Invoice::generateInvoiceNumber($branch->code),
                'payment_type' => $validated['payment_type'] ?? 'cash',
                'insurance_panel_id' => ($validated['payment_type'] ?? 'cash') === 'panel' ? ($validated['insurance_panel_id'] ?? null) : null,
                'patient_insurance_id' => ($validated['payment_type'] ?? 'cash') === 'panel' ? ($validated['patient_insurance_id'] ?? null) : null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'status' => 'issued',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('invoices.index')->with('success', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'branch', 'appointment', 'items.service', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        $branchId = session('current_branch_id');
        $patients = Patient::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        $services = Service::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'patients', 'services'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            $invoice->items()->delete();

            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $lineTotal;
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);
            }

            $invoice->update([
                'subtotal' => $subtotal,
                'tax' => $validated['tax'] ?? 0,
                'discount' => $validated['discount'] ?? 0,
                'total' => $subtotal + ($validated['tax'] ?? 0) - ($validated['discount'] ?? 0),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated.');
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['patient', 'branch', 'items.service', 'payments']);
        return view('invoices.print', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
