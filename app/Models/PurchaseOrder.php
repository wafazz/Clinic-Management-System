<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'branch_id', 'supplier_id', 'po_number', 'status', 'order_date', 'expected_date',
        'received_date', 'subtotal', 'tax', 'total_amount', 'notes', 'ordered_by', 'approved_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(PurchaseOrderItem::class); }
    public function orderedBy() { return $this->belongsTo(User::class, 'ordered_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public static function generateNumber($branchCode)
    {
        $prefix = 'PO-' . $branchCode . '-' . now()->format('Ym');
        $latest = static::where('po_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->po_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
