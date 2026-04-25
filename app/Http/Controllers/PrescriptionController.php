<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');

        $prescriptions = Prescription::where('branch_id', $branchId)
            ->with(['patient', 'doctor.user', 'items'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('patient', fn($q2) => $q2->where('name', 'like', "%{$request->search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $branchId = session('current_branch_id');
        $patients = Patient::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();
        $doctors = Doctor::where('branch_id', $branchId)->where('is_active', true)->with('user')->get();
        $medicines = Medicine::where('branch_id', $branchId)->where('is_active', true)->where('current_stock', '>', 0)->orderBy('name')->get();

        $selectedPatient = $request->filled('patient_id') ? $request->patient_id : null;
        $selectedAppointment = $request->filled('appointment_id') ? $request->appointment_id : null;

        return view('prescriptions.create', compact('patients', 'doctors', 'medicines', 'selectedPatient', 'selectedAppointment'));
    }

    public function store(Request $request)
    {
        $branchId = session('current_branch_id');

        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.dosage' => 'required|string|max:100',
            'items.*.frequency' => 'required|string|max:100',
            'items.*.duration' => 'required|string|max:100',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $branchId) {
            $prescription = Prescription::create([
                'branch_id' => $branchId,
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_id' => $request->appointment_id,
                'status' => 'draft',
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $item['medicine_id'],
                    'dosage' => $item['dosage'],
                    'frequency' => $item['frequency'],
                    'duration' => $item['duration'],
                    'quantity' => $item['quantity'],
                    'instructions' => $item['instructions'] ?? null,
                ]);
            }
        });

        return redirect()->route('prescriptions.index')->with('success', 'Prescription created successfully.');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor.user', 'appointment', 'items.medicine']);
        return view('prescriptions.show', compact('prescription'));
    }

    public function dispense(Prescription $prescription)
    {
        if ($prescription->status !== 'draft') {
            return back()->with('error', 'Only draft prescriptions can be dispensed.');
        }

        $prescription->load('items.medicine');

        foreach ($prescription->items as $item) {
            if ($item->medicine->current_stock < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->medicine->name}. Available: {$item->medicine->current_stock}, Required: {$item->quantity}");
            }
        }

        DB::transaction(function () use ($prescription) {
            foreach ($prescription->items as $item) {
                $medicine = $item->medicine;
                $stockBefore = $medicine->current_stock;
                $stockAfter = $stockBefore - $item->quantity;

                StockMovement::create([
                    'medicine_id' => $medicine->id,
                    'branch_id' => $prescription->branch_id,
                    'type' => 'dispensed',
                    'quantity' => -$item->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => "Prescription #{$prescription->id}",
                    'user_id' => auth()->id(),
                ]);

                $medicine->update(['current_stock' => $stockAfter]);
            }

            $prescription->update(['status' => 'dispensed']);
        });

        return back()->with('success', 'Prescription dispensed and stock updated.');
    }

    public function destroy(Prescription $prescription)
    {
        if ($prescription->status === 'dispensed') {
            return back()->with('error', 'Cannot delete a dispensed prescription.');
        }

        $prescription->items()->delete();
        $prescription->delete();

        return redirect()->route('prescriptions.index')->with('success', 'Prescription deleted.');
    }
}
