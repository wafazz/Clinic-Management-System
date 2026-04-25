<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    protected $fillable = ['stock_adjustment_id', 'medicine_id', 'quantity', 'batch_number', 'expiry_date', 'notes'];
    protected $casts = ['expiry_date' => 'date'];

    public function stockAdjustment() { return $this->belongsTo(StockAdjustment::class); }
    public function medicine() { return $this->belongsTo(Medicine::class); }
}
