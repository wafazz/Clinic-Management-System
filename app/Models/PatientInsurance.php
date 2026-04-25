<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientInsurance extends Model
{
    protected $fillable = [
        'patient_id', 'insurance_panel_id', 'member_id', 'policy_number',
        'company_name', 'department', 'effective_date', 'expiry_date',
        'remaining_limit', 'status', 'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'remaining_limit' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function panel()
    {
        return $this->belongsTo(InsurancePanel::class, 'insurance_panel_id');
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }
}
