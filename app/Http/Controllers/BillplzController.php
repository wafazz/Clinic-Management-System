<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillplzController extends Controller
{
    public function checkout(Invoice $invoice, BillplzService $billplz)
    {
        if (!$billplz->isConfigured()) {
            return back()->with('error', 'Billplz is not configured. Configure it in Settings first.');
        }

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice already paid.');
        }

        $balance = $invoice->total - $invoice->payments()->sum('amount');
        if ($balance <= 0) {
            return back()->with('error', 'No outstanding balance.');
        }

        $invoice->load('patient');
        $result = $billplz->createBill([
            'description' => 'Invoice ' . $invoice->invoice_number,
            'email' => $invoice->patient->email ?: 'no-reply@clinic.local',
            'name' => $invoice->patient->name,
            'amount' => $balance,
            'reference' => $invoice->invoice_number,
            'redirect_url' => route('billplz.redirect', ['invoice' => $invoice->id]),
        ]);

        if (!$result['success'] || !$result['url']) {
            return back()->with('error', 'Failed to create Billplz bill: ' . substr((string) ($result['response'] ?? 'unknown'), 0, 200));
        }

        // Save bill ID on invoice (using notes if no dedicated column)
        $invoice->update(['notes' => trim(($invoice->notes ?: '') . "\nBillplz Bill ID: " . $result['bill_id'])]);

        return redirect()->away($result['url']);
    }

    public function callback(Request $request, BillplzService $billplz)
    {
        $payload = $request->all();
        $signature = $request->header('X-Signature') ?? $request->input('x_signature');

        if (!$billplz->verifyCallback($payload, $signature)) {
            return response('Invalid signature', 403);
        }

        if (($payload['paid'] ?? null) !== 'true' && ($payload['state'] ?? '') !== 'paid') {
            return response('OK', 200);
        }

        // Match invoice by reference_1 (we set this to invoice_number)
        $invoiceNumber = $payload['reference_1'] ?? null;
        if (!$invoiceNumber) return response('No reference', 200);

        $invoice = Invoice::where('invoice_number', $invoiceNumber)->first();
        if (!$invoice) return response('Invoice not found', 200);

        $amount = isset($payload['amount']) ? (((int) $payload['amount']) / 100) : 0;

        DB::transaction(function () use ($invoice, $amount, $payload) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => 'online',
                'reference' => $payload['id'] ?? '',
                'paid_at' => now(),
            ]);

            $totalPaid = $invoice->payments()->sum('amount');
            $invoice->update([
                'status' => $totalPaid >= $invoice->total ? 'paid' : 'partial',
            ]);
        });

        return response('OK', 200);
    }

    public function redirect(Request $request, ?Invoice $invoice = null)
    {
        if ($invoice) {
            return redirect()->route('invoices.show', $invoice)->with('success', 'Returning from Billplz. Payment will reflect once confirmed.');
        }
        return redirect()->route('invoices.index')->with('success', 'Returning from Billplz.');
    }
}
