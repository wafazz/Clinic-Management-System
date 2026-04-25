<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleOverride extends Model
{
    protected $fillable = ['doctor_id', 'branch_id', 'date', 'is_available', 'start_time', 'end_time', 'reason'];
    protected $casts = ['date' => 'date', 'is_available' => 'boolean'];

    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
