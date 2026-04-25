<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = ['branch_id', 'adjustment_number', 'type', 'reason', 'adjusted_by', 'approved_by'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function items() { return $this->hasMany(StockAdjustmentItem::class); }
    public function adjustedBy() { return $this->belongsTo(User::class, 'adjusted_by'); }

    public static function generateNumber($branchCode)
    {
        $prefix = 'ADJ-' . $branchCode . '-' . now()->format('Ymd');
        $latest = static::where('adjustment_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->adjustment_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
