<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,card,bank_transfer,e_wallet',
            'reference' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['invoice_id'] = $invoice->id;
        Payment::create($validated);

        // Update invoice status
        $totalPaid = $invoice->payments()->sum('amount') + $validated['amount'];
        if ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded.');
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $payment->delete();

        // Recalculate status
        $totalPaid = $invoice->payments()->sum('amount');
        if ($totalPaid <= 0) {
            $invoice->update(['status' => 'issued']);
        } elseif ($totalPaid >= $invoice->total) {
            $invoice->update(['status' => 'paid']);
        } else {
            $invoice->update(['status' => 'partial']);
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment removed.');
    }
}
