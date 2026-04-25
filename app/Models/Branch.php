<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use Auditable;
    protected $fillable = [
        'name', 'code', 'address', 'phone', 'email',
        'opening_time', 'closing_time', 'is_active',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function locumSessions()
    {
        return $this->hasMany(LocumSession::class);
    }

    public function pharmacyCategories()
    {
        return $this->hasMany(PharmacyCategory::class);
    }

    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }

    public function labTests()
    {
        return $this->hasMany(LabTest::class);
    }

    public function labReports()
    {
        return $this->hasMany(LabReport::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }
}
