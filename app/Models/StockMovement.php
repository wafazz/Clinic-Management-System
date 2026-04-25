<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'medicine_id', 'branch_id', 'type', 'quantity',
        'stock_before', 'stock_after', 'reference', 'notes', 'user_id',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
