<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\NotifiesUsers;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use Auditable, NotifiesUsers;
    protected $fillable = [
        'branch_id', 'patient_id', 'appointment_id', 'consultation_id', 'invoice_number',
        'subtotal', 'tax', 'discount', 'total', 'status', 'notes',
        'payment_type', 'insurance_panel_id', 'patient_insurance_id',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function insurancePanel()
    {
        return $this->belongsTo(InsurancePanel::class);
    }

    public function patientInsurance()
    {
        return $this->belongsTo(PatientInsurance::class);
    }

    public function insuranceClaim()
    {
        return $this->hasOne(InsuranceClaim::class);
    }

    public function isPanelInvoice(): bool
    {
        return $this->payment_type === 'panel';
    }

    public static function generateInvoiceNumber($branchCode)
    {
        $prefix = $branchCode . '-INV-' . date('Ym');
        $latest = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($latest) {
            $number = (int) substr($latest->invoice_number, -4) + 1;
        } else {
            $number = 1;
        }

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getBalanceDueAttribute()
    {
        return $this->total - $this->total_paid;
    }
}
