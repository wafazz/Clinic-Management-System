<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');

        $doctors = Doctor::with(['user', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($search, function ($q, $search) {
                $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('mmc_number', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('doctors.index', compact('doctors', 'search'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        return view('doctors.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'mmc_number' => 'nullable|string|max:50',
            'apc_number' => 'nullable|string|max:50',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'doctor',
            'branch_id' => $validated['branch_id'],
            'is_active' => true,
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'specialization' => $validated['specialization'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'mmc_number' => $validated['mmc_number'] ?? null,
            'apc_number' => $validated['apc_number'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'branch', 'schedules', 'appointments.patient']);

        // Stats
        $totalAppointments = $doctor->appointments->count();
        $completedAppointments = $doctor->appointments->where('status', 'completed')->count();
        $upcomingAppointments = $doctor->appointments
            ->where('appointment_date', '>=', now()->toDateString())
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->take(5);
        $todayAppointments = $doctor->appointments
            ->filter(fn($a) => $a->appointment_date->isToday())
            ->count();

        $totalConsultations = \App\Models\Consultation::where('doctor_id', $doctor->id)->count();
        $monthlyRevenue = \App\Models\Invoice::whereHas('appointment', fn($q) => $q->where('doctor_id', $doctor->id))
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        // Patient count (distinct)
        $uniquePatients = $doctor->appointments->pluck('patient_id')->unique()->count();

        // Daily appointments last 14 days for sparkline
        $dailyTrend = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = $doctor->appointments->filter(fn($a) => $a->appointment_date->isSameDay($date))->count();
            $dailyTrend->push(['label' => $date->format('d/m'), 'count' => $count]);
        }

        return view('doctors.show', compact(
            'doctor', 'totalAppointments', 'completedAppointments', 'upcomingAppointments',
            'todayAppointments', 'totalConsultations', 'monthlyRevenue', 'uniquePatients', 'dailyTrend'
        ));
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        $branches = Branch::where('is_active', true)->get();
        return view('doctors.edit', compact('doctor', 'branches'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user_id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'branch_id' => 'required|exists:branches,id',
            'specialization' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'mmc_number' => 'nullable|string|max:50',
            'apc_number' => 'nullable|string|max:50',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'branch_id' => $validated['branch_id'],
        ];
        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        $doctor->user->update($userData);

        $doctor->update([
            'branch_id' => $validated['branch_id'],
            'specialization' => $validated['specialization'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'mmc_number' => $validated['mmc_number'] ?? null,
            'apc_number' => $validated['apc_number'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->user->delete();
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
