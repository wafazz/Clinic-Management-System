<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    protected $fillable = ['membership_id', 'patient_id', 'relationship', 'added_by', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function membership() { return $this->belongsTo(PatientMembership::class, 'membership_id'); }
    public function patient() { return $this->belongsTo(Patient::class); }
}
