<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_number', 'from_branch_id', 'to_branch_id', 'status',
        'requested_by', 'approved_by', 'received_by',
        'requested_at', 'approved_at', 'received_at', 'notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function fromBranch() { return $this->belongsTo(Branch::class, 'from_branch_id'); }
    public function toBranch() { return $this->belongsTo(Branch::class, 'to_branch_id'); }
    public function items() { return $this->hasMany(StockTransferItem::class); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }

    public static function generateNumber()
    {
        $prefix = 'TR-' . now()->format('Ymd');
        $latest = static::where('transfer_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->transfer_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
