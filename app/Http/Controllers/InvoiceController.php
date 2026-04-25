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
use App\Models\PatientMembership;
use App\Models\MembershipUsageLog;
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
        $prefillItems = [];
        $membership = null;
        if ($request->filled('consultation_id')) {
            $selectedConsultation = Consultation::with([
                'patient', 'doctor.user',
                'prescriptions.items.medicine',
                'labReports.items.test',
            ])->find($request->consultation_id);

            if ($selectedConsultation) {
                $selectedPatient = $selectedConsultation->patient_id;
                if (!$selectedAppointment && $selectedConsultation->appointment_id) {
                    $selectedAppointment = $selectedConsultation->appointment_id;
                }

                // Consultation fee (skip if free under membership)
                $membership = PatientMembership::where('patient_id', $selectedConsultation->patient_id)
                    ->where('status', 'active')
                    ->with('tier')
                    ->first();

                $consultationFee = (float) ($selectedConsultation->doctor->consultation_fee ?? 0);
                if ($consultationFee > 0) {
                    $prefillItems[] = [
                        'description' => 'Consultation - Dr. ' . $selectedConsultation->doctor->user->name,
                        'quantity' => 1,
                        'unit_price' => $consultationFee,
                    ];
                }

                // Dispensed medicines from prescriptions
                foreach ($selectedConsultation->prescriptions as $rx) {
                    if ($rx->status !== 'dispensed') continue;
                    foreach ($rx->items as $item) {
                        $prefillItems[] = [
                            'description' => $item->medicine->name . ' (' . $item->dosage . ' / ' . $item->frequency . ' / ' . $item->duration . ')',
                            'quantity' => $item->quantity,
                            'unit_price' => (float) ($item->medicine->selling_price ?? 0),
                        ];
                    }
                }

                // Lab tests from completed reports
                foreach ($selectedConsultation->labReports as $lab) {
                    foreach ($lab->items as $item) {
                        if (!$item->test) continue;
                        $prefillItems[] = [
                            'description' => 'Lab: ' . $item->test->name,
                            'quantity' => 1,
                            'unit_price' => (float) ($item->test->price ?? 0),
                        ];
                    }
                }
            }
        }
        return view('invoices.create', compact('patients', 'services', 'appointments', 'selectedAppointment', 'selectedConsultation', 'selectedPatient', 'prefillItems', 'membership', 'insurancePanels', 'patientInsurances'));
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
            'apply_membership_discount' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.kind' => 'nullable|in:consultation,medicine,lab,service,custom',
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);
        $branch = $patient->branch;

        $applyMembership = $request->boolean('apply_membership_discount');

        DB::transaction(function () use ($validated, $branch, $patient, $applyMembership, $request) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = $validated['tax'] ?? 0;
            $discount = $validated['discount'] ?? 0;

            // Apply membership tier discount automatically
            $membershipDiscount = 0;
            $membership = null;
            if ($applyMembership) {
                $membership = PatientMembership::where('patient_id', $patient->id)
                    ->where('status', 'active')
                    ->with('tier')
                    ->first();

                if ($membership && $membership->tier) {
                    $tier = $membership->tier;
                    foreach ($validated['items'] as $item) {
                        $kind = $item['kind'] ?? 'custom';
                        $lineTotal = $item['quantity'] * $item['unit_price'];
                        $rate = match ($kind) {
                            'consultation' => (float) $tier->discount_consultation,
                            'medicine' => (float) $tier->discount_medicine,
                            'lab' => (float) $tier->discount_lab,
                            default => 0,
                        };
                        $membershipDiscount += $lineTotal * ($rate / 100);
                    }
                }
            }

            $totalDiscount = $discount + $membershipDiscount;
            $total = $subtotal + $tax - $totalDiscount;

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
                'discount' => $totalDiscount,
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

            // Log membership usage
            if ($membership && $membershipDiscount > 0) {
                MembershipUsageLog::create([
                    'membership_id' => $membership->id,
                    'patient_id' => $patient->id,
                    'usage_type' => 'discount_applied',
                    'description' => $membership->tier->name . ' discount on invoice ' . $invoice->invoice_number,
                    'savings_amount' => $membershipDiscount,
                    'invoice_id' => $invoice->id,
                    'used_at' => now(),
                ]);
                $membership->increment('total_savings', $membershipDiscount);
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

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['patient', 'branch', 'items', 'payments', 'insurancePanel']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.receipt-pdf', compact('invoice'));
        return $pdf->stream("Receipt-{$invoice->invoice_number}.pdf");
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }
}
