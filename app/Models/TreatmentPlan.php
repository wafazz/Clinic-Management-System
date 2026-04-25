<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentPlan extends Model
{
    protected $fillable = [
        'patient_id', 'doctor_id', 'branch_id', 'consultation_id', 'template_id',
        'plan_number', 'title', 'diagnosis', 'description', 'total_sessions',
        'completed_sessions', 'status', 'start_date', 'expected_end_date',
        'actual_end_date', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function consultation() { return $this->belongsTo(Consultation::class); }
    public function template() { return $this->belongsTo(TreatmentPlanTemplate::class, 'template_id'); }
    public function sessions() { return $this->hasMany(TreatmentPlanSession::class); }

    public static function generateNumber()
    {
        $prefix = 'TP-' . now()->format('Ym');
        $latest = static::where('plan_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->plan_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
