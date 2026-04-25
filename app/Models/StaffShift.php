<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffShift extends Model
{
    protected $fillable = [
        'user_id', 'branch_id', 'shift_date', 'start_time', 'end_time',
        'shift_type', 'status', 'notes', 'created_by',
    ];

    protected $casts = ['shift_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
