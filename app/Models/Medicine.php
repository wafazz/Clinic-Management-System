<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use Auditable, NotifiesUsers;
    protected $fillable = [
        'branch_id', 'pharmacy_category_id', 'name', 'generic_name', 'sku',
        'unit', 'cost_price', 'selling_price', 'reorder_level', 'current_stock',
        'expiry_date', 'manufacturer', 'is_active',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function category()
    {
        return $this->belongsTo(PharmacyCategory::class, 'pharmacy_category_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
