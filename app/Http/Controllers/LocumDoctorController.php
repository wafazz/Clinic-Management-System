<?php

namespace App\Http\Controllers;

use App\Models\LocumDoctor;
use Illuminate\Http\Request;

class LocumDoctorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $locumDoctors = LocumDoctor::when($search, function ($q, $s) {
            $q->where('name', 'like', "%{$s}%")
              ->orWhere('specialization', 'like', "%{$s}%")
              ->orWhere('mmc_number', 'like', "%{$s}%");
        })->orderBy('name')->paginate(15)->withQueryString();

        return view('locum-doctors.index', compact('locumDoctors', 'search'));
    }

    public function create()
    {
        return view('locum-doctors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'ic_number' => 'nullable|string|max:20',
            'mmc_number' => 'nullable|string|max:50',
            'apc_number' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'session_rate' => 'nullable|numeric|min:0',
            'bank_details' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        LocumDoctor::create($validated);

        return redirect()->route('locum-doctors.index')->with('success', 'Locum doctor added.');
    }

    public function show(LocumDoctor $locumDoctor)
    {
        $locumDoctor->load(['sessions.branch']);
        return view('locum-doctors.show', compact('locumDoctor'));
    }

    public function edit(LocumDoctor $locumDoctor)
    {
        return view('locum-doctors.edit', compact('locumDoctor'));
    }

    public function update(Request $request, LocumDoctor $locumDoctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'ic_number' => 'nullable|string|max:20',
            'mmc_number' => 'nullable|string|max:50',
            'apc_number' => 'nullable|string|max:50',
            'specialization' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'session_rate' => 'nullable|numeric|min:0',
            'bank_details' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $locumDoctor->update($validated);

        return redirect()->route('locum-doctors.index')->with('success', 'Locum doctor updated.');
    }

    public function destroy(LocumDoctor $locumDoctor)
    {
        $locumDoctor->delete();
        return redirect()->route('locum-doctors.index')->with('success', 'Locum doctor deleted.');
    }
}
