<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\Branch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('current_branch_id');
        $orders = PurchaseOrder::with(['supplier', 'items'])
            ->where('branch_id', $branchId)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $branchId = session('current_branch_id');
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $medicines = Medicine::where('branch_id', $branchId)->where('is_active', true)->orderBy('name')->get();
        return view('purchase-orders.create', compact('suppliers', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        DB::transaction(function () use ($request, $branchId, $branch) {
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity_ordered'] * $item['cost_price'];
            }

            $po = PurchaseOrder::create([
                'branch_id' => $branchId,
                'supplier_id' => $request->supplier_id,
                'po_number' => PurchaseOrder::generateNumber($branch->code ?? 'BR'),
                'status' => 'draft',
                'order_date' => $request->order_date,
                'expected_date' => $request->expected_date,
                'subtotal' => $subtotal,
                'tax' => 0,
                'total_amount' => $subtotal,
                'notes' => $request->notes,
                'ordered_by' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity_ordered' => $item['quantity_ordered'],
                    'cost_price' => $item['cost_price'],
                    'total_price' => $item['quantity_ordered'] * $item['cost_price'],
                    'batch_number' => $item['batch_number'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'branch', 'items.medicine']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function receive(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Already received.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            foreach ($purchaseOrder->items as $item) {
                $item->update(['quantity_received' => $item->quantity_ordered]);

                $medicine = $item->medicine;
                $stockBefore = $medicine->current_stock;
                $stockAfter = $stockBefore + $item->quantity_ordered;

                StockMovement::create([
                    'medicine_id' => $medicine->id,
                    'branch_id' => $purchaseOrder->branch_id,
                    'type' => 'purchase',
                    'quantity' => $item->quantity_ordered,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => "PO #{$purchaseOrder->po_number}",
                    'user_id' => auth()->id(),
                ]);

                $medicine->update(['current_stock' => $stockAfter]);
            }

            $purchaseOrder->update([
                'status' => 'received',
                'received_date' => now(),
            ]);
        });

        return back()->with('success', 'Stock received.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Cannot delete a received PO.');
        }
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'PO deleted.');
    }
}
