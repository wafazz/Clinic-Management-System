<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use Auditable, NotifiesUsers;
    protected $fillable = [
        'branch_id', 'patient_id', 'doctor_id',
        'appointment_date', 'start_time', 'end_time',
        'status', 'reason', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
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

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function labReport()
    {
        return $this->hasOne(LabReport::class);
    }

    public function reminders()
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    public function queueEntry()
    {
        return $this->hasOne(WalkInQueue::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }
}
