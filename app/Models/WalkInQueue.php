<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalkInQueue extends Model
{
    protected $fillable = [
        'branch_id', 'patient_id', 'doctor_id', 'appointment_id',
        'queue_number', 'type', 'patient_name', 'patient_phone',
        'queue_date', 'reason', 'status', 'position', 'is_priority',
        'called_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_priority' => 'boolean',
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

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public static function generateQueueNumber($branchId, $date = null, $type = 'walk_in')
    {
        $date = $date ?? now()->toDateString();

        $latest = self::where('branch_id', $branchId)
            ->whereDate('queue_date', $date)
            ->orderBy('position', 'desc')
            ->first();

        $nextNumber = $latest ? $latest->position + 1 : 1;

        $prefix = $type === 'appointment' ? 'A' : 'W';
        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function getNextPosition($branchId, $date = null)
    {
        $date = $date ?? now()->toDateString();

        $latest = self::where('branch_id', $branchId)
            ->whereDate('queue_date', $date)
            ->max('position');

        return ($latest ?? 0) + 1;
    }

    public static function currentServing($branchId, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return self::where('branch_id', $branchId)
            ->whereDate('queue_date', $date)
            ->where('status', 'serving')
            ->orderBy('called_at', 'desc')
            ->first();
    }
}
