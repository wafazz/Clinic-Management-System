<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Branch;
use App\Http\Controllers\PatientPortalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');

        $patients = Patient::with('branch')
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('ic_number', 'like', "%{$search}%")
                      ->orWhere('patient_id', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact('patients', 'search'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        return view('patients.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'ic_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
        ]);

        $branch = Branch::findOrFail($validated['branch_id']);
        $validated['patient_id'] = Patient::generatePatientId($branch->code);
        $validated['is_active'] = $request->boolean('is_active', true);

        Patient::create($validated);

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient)
    {
        $patient->load(['branch', 'appointments.doctor.user', 'invoices', 'insurances.panel']);
        $branchId = session('current_branch_id');
        $insurancePanels = \App\Models\InsurancePanel::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('company_name')->get();

        // Stats
        $totalAppointments = $patient->appointments->count();
        $completedAppointments = $patient->appointments->where('status', 'completed')->count();
        $upcomingAppointments = $patient->appointments
            ->filter(fn($a) => $a->appointment_date >= now()->startOfDay() && !in_array($a->status, ['cancelled','no_show','completed']))
            ->count();
        $totalSpend = $patient->invoices->where('status', 'paid')->sum('total');
        $outstanding = $patient->invoices->whereIn('status', ['pending','partial'])->sum('total');
        $lastVisit = $patient->appointments
            ->where('status', 'completed')
            ->sortByDesc('appointment_date')
            ->first();
        $totalConsultations = \App\Models\Consultation::where('patient_id', $patient->id)->count();

        // 12-month trend
        $monthlyTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = $patient->appointments
                ->filter(fn($a) => $a->appointment_date->isSameMonth($month))
                ->count();
            $monthlyTrend->push(['label' => $month->format('M'), 'count' => $count]);
        }

        return view('patients.show', compact(
            'patient', 'insurancePanels', 'totalAppointments', 'completedAppointments',
            'upcomingAppointments', 'totalSpend', 'outstanding', 'lastVisit',
            'totalConsultations', 'monthlyTrend'
        ));
    }

    public function edit(Patient $patient)
    {
        $branches = Branch::where('is_active', true)->get();
        return view('patients.edit', compact('patient', 'branches'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'ic_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'blood_type' => 'nullable|string|max:5',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $patient->update($validated);

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Patient deleted successfully.');
    }

    public function enablePortalAccess(Request $request, Patient $patient)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ]);

        PatientPortalController::generatePortalAccess($patient, $request->password);

        return back()->with('success', 'Portal access enabled for ' . $patient->name . '. Password: ' . $request->password);
    }
}
