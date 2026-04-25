<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class LocumSession extends Model
{
    use Auditable;
    protected $fillable = [
        'locum_doctor_id', 'branch_id', 'session_date',
        'start_time', 'end_time', 'status', 'total_pay', 'is_paid', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function locumDoctor()
    {
        return $this->belongsTo(LocumDoctor::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
