<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'action', 'model_type', 'model_id',
        'description', 'old_values', 'new_values', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getModelNameAttribute()
    {
        $map = [
            'App\\Models\\Patient' => 'Patient',
            'App\\Models\\Appointment' => 'Appointment',
            'App\\Models\\Invoice' => 'Invoice',
            'App\\Models\\Doctor' => 'Doctor',
            'App\\Models\\Branch' => 'Branch',
            'App\\Models\\Service' => 'Service',
            'App\\Models\\Medicine' => 'Medicine',
            'App\\Models\\Prescription' => 'Prescription',
            'App\\Models\\LabReport' => 'Lab Report',
            'App\\Models\\LabTest' => 'Lab Test',
            'App\\Models\\InsurancePanel' => 'Insurance Panel',
            'App\\Models\\InsuranceClaim' => 'Insurance Claim',
            'App\\Models\\PatientInsurance' => 'Patient Insurance',
            'App\\Models\\LocumDoctor' => 'Locum Doctor',
            'App\\Models\\LocumSession' => 'Locum Session',
            'App\\Models\\User' => 'User',
            'App\\Models\\Setting' => 'Setting',
        ];

        return $map[$this->model_type] ?? class_basename($this->model_type);
    }

    public function getActionBadgeAttribute()
    {
        return match ($this->action) {
            'created' => 'badge-success',
            'updated' => 'badge-info',
            'deleted' => 'badge-danger',
            default => 'badge-secondary',
        };
    }
}
