<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    use Auditable, NotifiesUsers;
    protected $fillable = [
        'branch_id', 'patient_id', 'doctor_id', 'appointment_id', 'consultation_id', 'status', 'notes',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
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

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }
}
