<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    protected $fillable = ['package_id', 'item_type', 'item_id', 'description', 'quantity', 'unit_value'];

    public function package() { return $this->belongsTo(ServicePackage::class, 'package_id'); }
}
