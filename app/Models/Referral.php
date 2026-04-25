<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'branch_id', 'patient_id', 'consultation_id', 'referring_doctor_id',
        'referral_number', 'referred_to', 'specialty', 'reason', 'clinical_summary',
        'referral_date', 'urgency', 'status', 'notes',
    ];

    protected $casts = ['referral_date' => 'date'];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function consultation() { return $this->belongsTo(Consultation::class); }
    public function referringDoctor() { return $this->belongsTo(Doctor::class, 'referring_doctor_id'); }

    public static function generateNumber($branchCode)
    {
        $prefix = 'REF-' . $branchCode . '-' . now()->format('Ym');
        $latest = static::where('referral_number', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
        $next = $latest ? ((int) substr($latest->referral_number, -4)) + 1 : 1;
        return $prefix . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
