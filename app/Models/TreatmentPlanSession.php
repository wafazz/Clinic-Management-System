<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentPlanSession extends Model
{
    protected $fillable = [
        'treatment_plan_id', 'appointment_id', 'consultation_id', 'session_number',
        'title', 'description', 'scheduled_date', 'status', 'doctor_notes', 'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function treatmentPlan() { return $this->belongsTo(TreatmentPlan::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function consultation() { return $this->belongsTo(Consultation::class); }
}
