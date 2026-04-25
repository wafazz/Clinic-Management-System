<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $fillable = [
        'branch_id', 'name', 'slug', 'description', 'type', 'price', 'billing_cycle',
        'duration_days', 'max_visits', 'allow_partial_payment', 'min_deposit_amount',
        'min_deposit_percent', 'includes', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'includes' => 'array',
        'allow_partial_payment' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function items() { return $this->hasMany(PackageItem::class, 'package_id'); }
    public function subscriptions() { return $this->hasMany(PatientSubscription::class, 'package_id'); }
}
