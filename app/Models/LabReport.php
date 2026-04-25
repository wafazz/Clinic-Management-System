<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class LabReport extends Model
{
    use Auditable, NotifiesUsers;
    protected $fillable = [
        'branch_id', 'patient_id', 'doctor_id', 'appointment_id', 'consultation_id',
        'report_number', 'status', 'notes', 'reported_at',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    protected $casts = [
        'reported_at' => 'datetime',
    ];

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

    public function items()
    {
        return $this->hasMany(LabReportItem::class);
    }

    public static function generateReportNumber($branchCode)
    {
        $latest = static::where('report_number', 'like', "LAB-{$branchCode}-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest) {
            $lastNumber = (int) substr($latest->report_number, strrpos($latest->report_number, '-') + 1);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'LAB-' . $branchCode . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
