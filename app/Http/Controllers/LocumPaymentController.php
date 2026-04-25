<?php

namespace App\Http\Controllers;

use App\Models\LocumPayment;
use App\Models\LocumPaymentItem;
use App\Models\LocumDoctor;
use App\Models\LocumSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocumPaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = LocumPayment::with('locumDoctor')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()->paginate(15)->withQueryString();
        return view('locum-payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $locumDoctors = LocumDoctor::where('is_active', true)->orderBy('name')->get();
        $unpaidSessions = collect();
        $selectedDoctor = null;
        if ($request->filled('locum_doctor_id')) {
            $selectedDoctor = LocumDoctor::find($request->locum_doctor_id);
            $unpaidSessions = LocumSession::where('locum_doctor_id', $request->locum_doctor_id)
                ->where('is_paid', false)
                ->orderBy('session_date')
                ->get();
        }
        return view('locum-payments.create', compact('locumDoctors', 'unpaidSessions', 'selectedDoctor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'locum_doctor_id' => 'required|exists:locum_doctors,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'session_ids' => 'required|array|min:1',
            'session_ids.*' => 'exists:locum_sessions,id',
            'deductions' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $sessions = LocumSession::whereIn('id', $request->session_ids)->get();
            $gross = $sessions->sum('total_amount');
            $deductions = $request->deductions ?? 0;
            $net = $gross - $deductions;

            $payment = LocumPayment::create([
                'locum_doctor_id' => $request->locum_doctor_id,
                'payment_number' => LocumPayment::generateNumber(),
                'period_start' => $request->period_start,
                'period_end' => $request->period_end,
                'total_sessions' => $sessions->count(),
                'gross_amount' => $gross,
                'deductions' => $deductions,
                'net_amount' => $net,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            foreach ($sessions as $session) {
                LocumPaymentItem::create([
                    'locum_payment_id' => $payment->id,
                    'locum_session_id' => $session->id,
                    'session_date' => $session->session_date,
                    'rate_amount' => $session->total_pay ?? 0,
                    'subtotal' => $session->total_pay ?? 0,
                ]);
            }
        });

        return redirect()->route('locum-payments.index')->with('success', 'Locum payment created.');
    }

    public function show(LocumPayment $locumPayment)
    {
        $locumPayment->load(['locumDoctor', 'items.locumSession']);
        return view('locum-payments.show', compact('locumPayment'));
    }

    public function markPaid(LocumPayment $locumPayment)
    {
        DB::transaction(function () use ($locumPayment) {
            $locumPayment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'approved_by' => auth()->id(),
            ]);
            // Mark sessions as paid
            $sessionIds = $locumPayment->items->pluck('locum_session_id');
            LocumSession::whereIn('id', $sessionIds)->update(['is_paid' => true]);
        });
        return back()->with('success', 'Payment marked as paid.');
    }

    public function destroy(LocumPayment $locumPayment)
    {
        if ($locumPayment->status === 'paid') {
            return back()->with('error', 'Cannot delete a paid payment.');
        }
        $locumPayment->delete();
        return redirect()->route('locum-payments.index')->with('success', 'Payment deleted.');
    }
}
