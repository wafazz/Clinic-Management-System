<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class LocumDoctor extends Model
{
    use Auditable;
    protected $fillable = [
        'name', 'email', 'phone', 'ic_number', 'mmc_number',
        'apc_number', 'specialization', 'hourly_rate', 'session_rate',
        'bank_details', 'is_active', 'password', 'last_login_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function sessions()
    {
        return $this->hasMany(LocumSession::class);
    }
}
