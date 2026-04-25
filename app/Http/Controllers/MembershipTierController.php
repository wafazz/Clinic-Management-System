<?php

namespace App\Http\Controllers;

use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MembershipTierController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::orderBy('sort_order')->orderBy('price')->paginate(15);
        return view('membership-tiers.index', compact('tiers'));
    }

    public function create() { return view('membership-tiers.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:free,monthly,yearly',
            'discount_consultation' => 'nullable|numeric|min:0|max:100',
            'discount_medicine' => 'nullable|numeric|min:0|max:100',
            'discount_lab' => 'nullable|numeric|min:0|max:100',
            'free_consultations_per_year' => 'nullable|integer|min:0',
            'free_lab_tests_per_year' => 'nullable|integer|min:0',
            'priority_queue' => 'nullable|boolean',
            'max_family_members' => 'nullable|integer|min:0',
        ]);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['priority_queue'] = $request->boolean('priority_queue');
        $validated['is_active'] = true;
        MembershipTier::create($validated);
        return redirect()->route('membership-tiers.index')->with('success', 'Tier created.');
    }

    public function edit(MembershipTier $membershipTier) { return view('membership-tiers.edit', compact('membershipTier')); }

    public function update(Request $request, MembershipTier $membershipTier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:free,monthly,yearly',
            'discount_consultation' => 'nullable|numeric|min:0|max:100',
            'discount_medicine' => 'nullable|numeric|min:0|max:100',
            'discount_lab' => 'nullable|numeric|min:0|max:100',
            'free_consultations_per_year' => 'nullable|integer|min:0',
            'free_lab_tests_per_year' => 'nullable|integer|min:0',
            'max_family_members' => 'nullable|integer|min:0',
        ]);
        $validated['priority_queue'] = $request->boolean('priority_queue');
        $validated['is_active'] = $request->boolean('is_active');
        $membershipTier->update($validated);
        return redirect()->route('membership-tiers.index')->with('success', 'Tier updated.');
    }

    public function destroy(MembershipTier $membershipTier)
    {
        $membershipTier->delete();
        return redirect()->route('membership-tiers.index')->with('success', 'Tier deleted.');
    }
}
