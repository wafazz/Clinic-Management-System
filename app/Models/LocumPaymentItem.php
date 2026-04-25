<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocumPaymentItem extends Model
{
    protected $fillable = ['locum_payment_id', 'locum_session_id', 'session_date', 'rate_amount', 'subtotal'];
    protected $casts = ['session_date' => 'date'];

    public function locumPayment() { return $this->belongsTo(LocumPayment::class); }
    public function locumSession() { return $this->belongsTo(LocumSession::class); }
}
