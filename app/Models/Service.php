<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Auditable;
    protected $fillable = [
        'branch_id', 'name', 'description', 'price', 'category', 'is_active',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
