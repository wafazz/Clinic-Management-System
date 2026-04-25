<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocumPayment extends Model
{
    protected $fillable = [
        'locum_doctor_id', 'payment_number', 'period_start', 'period_end',
        'total_sessions', 'gross_amount', 'deductions', 'net_amount',
        'status', 'payment_method', 'payment_reference', 'approved_by', 'paid_at', 'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
    ];

    public function locumDoctor() { return $this->belongsTo(LocumDoctor::class); }
    public function items() { return $this->hasMany(LocumPaymentItem::class); }

    public static function generateNumber()
    {
        $prefix = 'LP-' . now()->format('Ym');
        $latest = static::where('payment_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->payment_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
