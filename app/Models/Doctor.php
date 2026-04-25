<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use Auditable;
    protected $fillable = [
        'user_id', 'branch_id', 'specialization', 'qualification',
        'mmc_number', 'apc_number', 'consultation_fee', 'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
