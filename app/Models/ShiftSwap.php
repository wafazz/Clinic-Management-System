<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShiftSwap extends Model
{
    protected $fillable = ['requester_shift_id', 'target_user_id', 'target_shift_id', 'reason', 'status', 'approved_by', 'approved_at'];
    protected $casts = ['approved_at' => 'datetime'];

    public function requesterShift() { return $this->belongsTo(StaffShift::class, 'requester_shift_id'); }
    public function targetUser() { return $this->belongsTo(User::class, 'target_user_id'); }
}
