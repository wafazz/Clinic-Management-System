<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'assigned_to', 'branch_id', 'name', 'phone', 'email', 'ic_number', 'gender', 'date_of_birth',
        'source', 'service_interest', 'status', 'notes', 'last_followup_notes',
        'last_contacted_at', 'next_followup_at', 'converted_at', 'patient_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_contacted_at' => 'datetime',
        'next_followup_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
}
