<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $fillable = ['stock_transfer_id', 'medicine_id', 'quantity', 'batch_number', 'expiry_date'];
    protected $casts = ['expiry_date' => 'date'];

    public function stockTransfer() { return $this->belongsTo(StockTransfer::class); }
    public function medicine() { return $this->belongsTo(Medicine::class); }
}
