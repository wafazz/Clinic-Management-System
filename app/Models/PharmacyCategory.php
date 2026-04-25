<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyCategory extends Model
{
    protected $fillable = ['branch_id', 'name', 'description', 'is_active'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}
