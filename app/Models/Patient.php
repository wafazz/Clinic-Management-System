<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use Auditable;
    protected $fillable = [
        'branch_id', 'patient_id', 'name', 'ic_number', 'gender',
        'date_of_birth', 'phone', 'email', 'address',
        'emergency_contact', 'emergency_phone', 'allergies',
        'medical_history', 'blood_type', 'is_active',
        'portal_token', 'portal_token_expires_at', 'password', 'last_portal_login',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'portal_token_expires_at' => 'datetime',
            'last_portal_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function labReports()
    {
        return $this->hasMany(LabReport::class);
    }

    public function insurances()
    {
        return $this->hasMany(PatientInsurance::class);
    }

    public function activeInsurance()
    {
        return $this->hasMany(PatientInsurance::class)->where('status', 'active');
    }

    public function insuranceClaims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }

    public static function generatePatientId($branchCode)
    {
        $latest = self::where('patient_id', 'like', $branchCode . '-P%')
            ->orderBy('patient_id', 'desc')
            ->first();

        if ($latest) {
            $number = (int) substr($latest->patient_id, strlen($branchCode) + 2) + 1;
        } else {
            $number = 1;
        }

        return $branchCode . '-P' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
