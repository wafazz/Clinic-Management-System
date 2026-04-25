<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use Auditable, NotifiesUsers;

    protected $fillable = [
        'consultation_number', 'branch_id', 'patient_id', 'doctor_id',
        'appointment_id', 'walk_in_queue_id',
        'locum_doctor_id', 'locum_invitation_id',
        'bp_systolic', 'bp_diastolic', 'pulse', 'temperature',
        'weight_kg', 'height_cm', 'bmi', 'spo2', 'respiratory_rate',
        'chief_complaint', 'history', 'examination', 'diagnosis',
        'treatment_plan', 'notes', 'follow_up_date',
        'mc_issued', 'mc_from', 'mc_to', 'mc_days', 'mc_reason',
        'status', 'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'mc_from' => 'date',
            'mc_to' => 'date',
            'mc_issued' => 'boolean',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function walkInQueue()
    {
        return $this->belongsTo(WalkInQueue::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function labReports()
    {
        return $this->hasMany(LabReport::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function bpReading()
    {
        if ($this->bp_systolic && $this->bp_diastolic) {
            return $this->bp_systolic . '/' . $this->bp_diastolic;
        }
        return null;
    }

    public static function generateNumber($branchCode)
    {
        $prefix = 'CN-' . $branchCode . '-' . now()->format('Ymd');
        $latest = static::where('consultation_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latest) {
            $lastNumber = (int) substr($latest->consultation_number, strrpos($latest->consultation_number, '-') + 1);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
