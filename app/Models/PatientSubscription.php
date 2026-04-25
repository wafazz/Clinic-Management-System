<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientSubscription extends Model
{
    protected $fillable = [
        'patient_id', 'package_id', 'branch_id', 'subscription_number', 'status',
        'payment_mode', 'total_amount', 'deposit_amount', 'balance_amount',
        'per_session_amount', 'total_paid', 'start_date', 'end_date', 'next_billing_date',
        'visits_total', 'visits_used', 'payment_method', 'cancelled_at', 'cancel_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function package() { return $this->belongsTo(ServicePackage::class, 'package_id'); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function payments() { return $this->hasMany(SubscriptionPayment::class, 'subscription_id'); }
    public function usages() { return $this->hasMany(SubscriptionUsage::class, 'subscription_id'); }

    public static function generateNumber()
    {
        $prefix = 'SUB-' . now()->format('Ym');
        $latest = static::where('subscription_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->subscription_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
