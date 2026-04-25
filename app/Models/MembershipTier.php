<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'billing_cycle', 'benefits',
        'discount_consultation', 'discount_medicine', 'discount_lab',
        'free_consultations_per_year', 'free_lab_tests_per_year',
        'priority_queue', 'max_family_members', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'benefits' => 'array',
        'priority_queue' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function memberships() { return $this->hasMany(PatientMembership::class, 'tier_id'); }
}
