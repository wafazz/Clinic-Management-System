<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\PharmacyCategory;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');

        $medicines = Medicine::where('branch_id', $branchId)
            ->with('category')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('generic_name', 'like', "%{$search}%")
                       ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($filter === 'low_stock', fn($q) => $q->whereColumn('current_stock', '<=', 'reorder_level'))
            ->when($filter === 'expired', fn($q) => $q->whereNotNull('expiry_date')->where('expiry_date', '<', now()))
            ->when($request->filled('category_id'), fn($q) => $q->where('pharmacy_category_id', $request->category_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = PharmacyCategory::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();

        return view('pharmacy.medicines.index', compact('medicines', 'categories', 'search', 'filter'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $categories = PharmacyCategory::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        return view('pharmacy.medicines.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_category_id' => 'nullable|exists:pharmacy_categories,id',
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:medicines,sku',
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'manufacturer' => 'nullable|string|max:255',
        ]);

        $validated['branch_id'] = session('current_branch_id');
        $validated['is_active'] = $request->boolean('is_active', true);

        $medicine = Medicine::create($validated);

        if ($validated['current_stock'] > 0) {
            StockMovement::create([
                'medicine_id' => $medicine->id,
                'branch_id' => $validated['branch_id'],
                'type' => 'purchase',
                'quantity' => $validated['current_stock'],
                'stock_before' => 0,
                'stock_after' => $validated['current_stock'],
                'reference' => 'Initial stock',
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('medicines.index')->with('success', 'Medicine added successfully.');
    }

    public function show(Medicine $medicine)
    {
        $medicine->load(['category', 'stockMovements' => fn($q) => $q->with('user')->latest()->limit(20)]);
        return view('pharmacy.medicines.show', compact('medicine'));
    }

    public function edit(Medicine $medicine)
    {
        $branchId = session('current_branch_id');
        $categories = PharmacyCategory::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();
        return view('pharmacy.medicines.edit', compact('medicine', 'categories'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'pharmacy_category_id' => 'nullable|exists:pharmacy_categories,id',
            'name' => 'required|string|max:255',
            'generic_name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:medicines,sku,' . $medicine->id,
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'manufacturer' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $medicine->update($validated);

        return redirect()->route('medicines.index')->with('success', 'Medicine updated successfully.');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return redirect()->route('medicines.index')->with('success', 'Medicine deleted successfully.');
    }

    public function adjustStock(Request $request, Medicine $medicine)
    {
        $request->validate([
            'type' => 'required|in:purchase,adjustment,return,expired',
            'quantity' => 'required|integer|not_in:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $quantity = (int) $request->quantity;
        if (in_array($request->type, ['expired']) && $quantity > 0) {
            $quantity = -$quantity;
        }

        $stockBefore = $medicine->current_stock;
        $stockAfter = $stockBefore + $quantity;

        if ($stockAfter < 0) {
            return back()->with('error', 'Insufficient stock. Current stock: ' . $stockBefore);
        }

        StockMovement::create([
            'medicine_id' => $medicine->id,
            'branch_id' => $medicine->branch_id,
            'type' => $request->type,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'notes' => $request->notes,
            'user_id' => auth()->id(),
        ]);

        $medicine->update(['current_stock' => $stockAfter]);

        return back()->with('success', 'Stock adjusted successfully. New stock: ' . $stockAfter);
    }
}
