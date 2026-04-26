<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Medicine;
use App\Models\Branch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $adjustments = StockAdjustment::with('items')
            ->where('branch_id', $branchId)
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->latest()->paginate(15)->withQueryString();
        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $medicines = Medicine::when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)->orderBy('name')->get();
        return view('stock-adjustments.create', compact('medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:adjustment_in,adjustment_out,expired,damaged',
            'reason' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        DB::transaction(function () use ($request, $branchId, $branch) {
            $adj = StockAdjustment::create([
                'branch_id' => $branchId,
                'adjustment_number' => StockAdjustment::generateNumber($branch->code ?? 'BR'),
                'type' => $request->type,
                'reason' => $request->reason,
                'adjusted_by' => auth()->id(),
            ]);

            $isIn = $request->type === 'adjustment_in';
            foreach ($request->items as $item) {
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adj->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                    'notes' => $item['notes'] ?? null,
                ]);

                $med = Medicine::find($item['medicine_id']);
                $delta = $isIn ? $item['quantity'] : -$item['quantity'];
                $stockBefore = $med->current_stock;
                $stockAfter = $stockBefore + $delta;

                StockMovement::create([
                    'medicine_id' => $med->id,
                    'branch_id' => $branchId,
                    'type' => $isIn ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => $delta,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => "ADJ {$adj->adjustment_number}",
                    'user_id' => auth()->id(),
                ]);

                $med->update(['current_stock' => $stockAfter]);
            }
        });

        return redirect()->route('stock-adjustments.index')->with('success', 'Stock adjusted.');
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['items.medicine', 'branch']);
        return view('stock-adjustments.show', compact('stockAdjustment'));
    }
}
