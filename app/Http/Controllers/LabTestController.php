<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use Illuminate\Http\Request;

class LabTestController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');

        $labTests = LabTest::where('branch_id', $branchId)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn($q) => $q->where('category', $request->category))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = LabTest::where('branch_id', $branchId)->whereNotNull('category')->distinct()->pluck('category');

        return view('lab.tests.index', compact('labTests', 'categories', 'search'));
    }

    public function create()
    {
        return view('lab.tests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'normal_range' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['branch_id'] = session('current_branch_id');
        $validated['is_active'] = $request->boolean('is_active', true);

        LabTest::create($validated);

        return redirect()->route('lab-tests.index')->with('success', 'Lab test created successfully.');
    }

    public function edit(LabTest $labTest)
    {
        return view('lab.tests.edit', compact('labTest'));
    }

    public function update(Request $request, LabTest $labTest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'nullable|string|max:100',
            'normal_range' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $labTest->update($validated);

        return redirect()->route('lab-tests.index')->with('success', 'Lab test updated successfully.');
    }

    public function destroy(LabTest $labTest)
    {
        if ($labTest->reportItems()->exists()) {
            return back()->with('error', 'Cannot delete test used in lab reports.');
        }

        $labTest->delete();
        return redirect()->route('lab-tests.index')->with('success', 'Lab test deleted successfully.');
    }
}
