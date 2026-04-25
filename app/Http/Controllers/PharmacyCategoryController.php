<?php

namespace App\Http\Controllers;

use App\Models\PharmacyCategory;
use Illuminate\Http\Request;

class PharmacyCategoryController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');

        $categories = PharmacyCategory::where('branch_id', $branchId)
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->withCount('medicines')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('pharmacy.categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('pharmacy.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['branch_id'] = session('current_branch_id');
        $validated['is_active'] = $request->boolean('is_active', true);

        PharmacyCategory::create($validated);

        return redirect()->route('pharmacy-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(PharmacyCategory $pharmacyCategory)
    {
        return view('pharmacy.categories.edit', compact('pharmacyCategory'));
    }

    public function update(Request $request, PharmacyCategory $pharmacyCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $pharmacyCategory->update($validated);

        return redirect()->route('pharmacy-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(PharmacyCategory $pharmacyCategory)
    {
        if ($pharmacyCategory->medicines()->exists()) {
            return back()->with('error', 'Cannot delete category with medicines.');
        }

        $pharmacyCategory->delete();
        return redirect()->route('pharmacy-categories.index')->with('success', 'Category deleted successfully.');
    }
}
