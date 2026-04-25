<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipUsageLog extends Model
{
    protected $fillable = ['membership_id', 'patient_id', 'usage_type', 'description', 'savings_amount', 'invoice_id', 'used_at'];
    protected $casts = ['used_at' => 'datetime'];

    public function membership() { return $this->belongsTo(PatientMembership::class, 'membership_id'); }
}
