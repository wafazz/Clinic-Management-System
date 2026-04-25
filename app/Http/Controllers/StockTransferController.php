<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Branch;
use App\Models\Medicine;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $transfers = StockTransfer::with(['fromBranch', 'toBranch', 'items'])
            ->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)->orWhere('to_branch_id', $branchId);
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $branches = Branch::where('id', '!=', $branchId)->get();
        $medicines = Medicine::where('branch_id', $branchId)->where('current_stock', '>', 0)->orderBy('name')->get();
        return view('stock-transfers.create', compact('branches', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'to_branch_id' => 'required|exists:branches,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $branchId = session('current_branch_id');

        DB::transaction(function () use ($request, $branchId) {
            $transfer = StockTransfer::create([
                'transfer_number' => StockTransfer::generateNumber(),
                'from_branch_id' => $branchId,
                'to_branch_id' => $request->to_branch_id,
                'status' => 'pending',
                'requested_by' => auth()->id(),
                'requested_at' => now(),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
        });

        return redirect()->route('stock-transfers.index')->with('success', 'Transfer requested.');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromBranch', 'toBranch', 'items.medicine']);
        return view('stock-transfers.show', compact('stockTransfer'));
    }

    public function receive(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status === 'received') {
            return back()->with('error', 'Already received.');
        }

        DB::transaction(function () use ($stockTransfer) {
            foreach ($stockTransfer->items as $item) {
                // Deduct from source
                $sourceMed = Medicine::where('branch_id', $stockTransfer->from_branch_id)
                    ->where('id', $item->medicine_id)->first();
                if ($sourceMed) {
                    $sourceMed->decrement('current_stock', $item->quantity);
                    StockMovement::create([
                        'medicine_id' => $sourceMed->id,
                        'branch_id' => $stockTransfer->from_branch_id,
                        'type' => 'transfer_out',
                        'quantity' => -$item->quantity,
                        'stock_before' => $sourceMed->current_stock + $item->quantity,
                        'stock_after' => $sourceMed->current_stock,
                        'reference' => "Transfer {$stockTransfer->transfer_number}",
                        'user_id' => auth()->id(),
                    ]);
                }

                // Add to destination (find or create medicine in target branch)
                $destMed = Medicine::where('branch_id', $stockTransfer->to_branch_id)
                    ->where('name', $item->medicine->name)->first();
                if ($destMed) {
                    $destMed->increment('current_stock', $item->quantity);
                    StockMovement::create([
                        'medicine_id' => $destMed->id,
                        'branch_id' => $stockTransfer->to_branch_id,
                        'type' => 'transfer_in',
                        'quantity' => $item->quantity,
                        'stock_before' => $destMed->current_stock - $item->quantity,
                        'stock_after' => $destMed->current_stock,
                        'reference' => "Transfer {$stockTransfer->transfer_number}",
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            $stockTransfer->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);
        });

        return back()->with('success', 'Transfer received and stock updated.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status === 'received') {
            return back()->with('error', 'Cannot delete a received transfer.');
        }
        $stockTransfer->delete();
        return redirect()->route('stock-transfers.index')->with('success', 'Transfer deleted.');
    }
}
