<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TreatmentPlanTemplate extends Model
{
    protected $fillable = ['name', 'diagnosis', 'description', 'total_sessions', 'interval_days', 'session_defaults', 'is_active', 'created_by'];

    protected $casts = [
        'session_defaults' => 'array',
        'is_active' => 'boolean',
    ];
}
