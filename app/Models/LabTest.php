<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabTest extends Model
{
    protected $fillable = [
        'branch_id', 'name', 'description', 'category', 'normal_range', 'unit', 'price', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function reportItems()
    {
        return $this->hasMany(LabReportItem::class);
    }
}
