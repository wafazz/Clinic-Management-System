<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientMembership extends Model
{
    protected $fillable = [
        'patient_id', 'tier_id', 'membership_number', 'status', 'start_date', 'end_date',
        'auto_renew', 'next_billing_date', 'free_consultations_used', 'free_lab_tests_used',
        'total_savings', 'payment_method', 'cancelled_at', 'cancel_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_date' => 'date',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function tier() { return $this->belongsTo(MembershipTier::class, 'tier_id'); }
    public function familyMembers() { return $this->hasMany(FamilyMember::class, 'membership_id'); }
    public function usageLogs() { return $this->hasMany(MembershipUsageLog::class, 'membership_id'); }

    public static function generateNumber()
    {
        $prefix = 'MBR-' . now()->format('Y');
        $latest = static::where('membership_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->membership_number, -5)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
