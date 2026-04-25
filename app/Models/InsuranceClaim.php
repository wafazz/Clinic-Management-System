<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    use Auditable, NotifiesUsers;
    protected $fillable = [
        'branch_id', 'invoice_id', 'patient_id', 'insurance_panel_id',
        'patient_insurance_id', 'claim_number', 'gl_number', 'gl_status',
        'claim_amount', 'approved_amount', 'patient_copay', 'status',
        'submission_date', 'approval_date', 'payment_date', 'payment_reference',
        'rejection_reason', 'notes',
    ];

    protected $casts = [
        'claim_amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'patient_copay' => 'decimal:2',
        'submission_date' => 'date',
        'approval_date' => 'date',
        'payment_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function panel()
    {
        return $this->belongsTo(InsurancePanel::class, 'insurance_panel_id');
    }

    public function patientInsurance()
    {
        return $this->belongsTo(PatientInsurance::class);
    }

    public static function generateClaimNumber($branchCode)
    {
        $latest = static::where('claim_number', 'like', "CLM-{$branchCode}-%")
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $latest
            ? (int) substr($latest->claim_number, strrpos($latest->claim_number, '-') + 1) + 1
            : 1;

        return 'CLM-' . $branchCode . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
