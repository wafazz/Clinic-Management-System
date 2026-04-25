<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocumInvitation extends Model
{
    protected $fillable = [
        'locum_doctor_id', 'branch_id', 'valid_from', 'valid_to',
        'can_consultation', 'can_treatment_plan', 'treatment_plan_requires_approval',
        'status', 'accepted_at', 'revoked_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'can_consultation' => 'boolean',
        'can_treatment_plan' => 'boolean',
        'treatment_plan_requires_approval' => 'boolean',
    ];

    public function locumDoctor() { return $this->belongsTo(LocumDoctor::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Active = accepted + currently within valid period.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'accepted') return false;
        $now = now();
        return $now->between($this->valid_from, $this->valid_to);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && now()->lessThan($this->valid_to);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->valid_to);
    }

    /**
     * Get the active invitation for a given locum doctor (if any).
     */
    public static function activeFor(int $locumDoctorId): ?self
    {
        return static::where('locum_doctor_id', $locumDoctorId)
            ->where('status', 'accepted')
            ->where('valid_from', '<=', now())
            ->where('valid_to', '>=', now())
            ->latest()
            ->first();
    }
}
