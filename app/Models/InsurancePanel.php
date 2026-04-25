<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class InsurancePanel extends Model
{
    use Auditable;
    protected $fillable = [
        'branch_id', 'company_name', 'type', 'contact_person', 'phone', 'email',
        'address', 'credit_terms', 'consultation_limit', 'annual_limit',
        'covered_services', 'exclusions', 'notes', 'requires_gl', 'is_active',
    ];

    protected $casts = [
        'consultation_limit' => 'decimal:2',
        'annual_limit' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function patientInsurances()
    {
        return $this->hasMany(PatientInsurance::class);
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
