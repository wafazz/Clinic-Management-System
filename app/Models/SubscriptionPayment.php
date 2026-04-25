<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'subscription_id', 'payment_type', 'session_number', 'amount', 'payment_method',
        'reference_number', 'status', 'due_date', 'paid_at', 'received_by', 'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function subscription() { return $this->belongsTo(PatientSubscription::class, 'subscription_id'); }
}
