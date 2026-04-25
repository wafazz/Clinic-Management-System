<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'medicine_id', 'quantity_ordered', 'quantity_received',
        'cost_price', 'total_price', 'batch_number', 'expiry_date',
    ];

    protected $casts = ['expiry_date' => 'date'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function medicine() { return $this->belongsTo(Medicine::class); }
}
