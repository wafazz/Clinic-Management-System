<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $branches = Branch::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        })->orderBy('name')->paginate(15)->withQueryString();

        return view('branches.index', compact('branches', 'search'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    public function show(Branch $branch)
    {
        $branch->loadCount(['patients', 'doctors', 'appointments', 'invoices']);

        $today = now()->toDateString();

        $todayAppointments = $branch->appointments()->whereDate('appointment_date', $today)->count();
        $todayCompleted = $branch->appointments()->whereDate('appointment_date', $today)->where('status', 'completed')->count();
        $todayPending = $branch->appointments()->whereDate('appointment_date', $today)->whereIn('status', ['pending', 'confirmed'])->count();
        $todayRevenue = \App\Models\Invoice::where('branch_id', $branch->id)
            ->whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('total');
        $monthRevenue = \App\Models\Invoice::where('branch_id', $branch->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'paid')
            ->sum('total');

        $topDoctors = \App\Models\Doctor::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->withCount('appointments')
            ->with('user')
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        $recentAppointments = $branch->appointments()
            ->with(['patient', 'doctor.user'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->take(8)
            ->get();

        $trend = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = $branch->appointments()->whereDate('appointment_date', $date->toDateString())->count();
            $trend->push(['label' => $date->format('d/m'), 'count' => $count]);
        }

        return view('branches.show', compact(
            'branch', 'todayAppointments', 'todayCompleted', 'todayPending',
            'todayRevenue', 'monthRevenue', 'topDoctors', 'recentAppointments', 'trend'
        ));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted successfully.');
    }
}
