<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    protected $fillable = [
        'subscription_id', 'appointment_id', 'consultation_id', 'package_item_id',
        'subscription_payment_id', 'item_type', 'description', 'quantity_used', 'used_at', 'recorded_by',
    ];

    protected $casts = ['used_at' => 'datetime'];

    public function subscription() { return $this->belongsTo(PatientSubscription::class, 'subscription_id'); }
}
