<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #1f2937; padding: 30px; font-size: 12px; }
        .header { border-bottom: 3px solid #0ea5e9; padding-bottom: 12px; margin-bottom: 20px; overflow: hidden; }
        .header .clinic-name { font-size: 22px; font-weight: bold; color: #0ea5e9; margin: 0; }
        .header .clinic-meta { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .header .right { float: right; text-align: right; }
        .header .right .invoice-no { font-size: 20px; font-weight: bold; color: #1f2937; }
        .header .right .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .meta-grid { width: 100%; margin-bottom: 18px; }
        .meta-grid td { padding: 6px 0; vertical-align: top; }
        .meta-grid .label { color: #6b7280; font-size: 10px; text-transform: uppercase; }
        .meta-grid .value { font-weight: bold; color: #1f2937; }
        table.items { width: 100%; border-collapse: collapse; margin: 18px 0; }
        table.items th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 11px; border-bottom: 2px solid #e5e7eb; }
        table.items td { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
        table.items .text-right { text-align: right; }
        .totals { width: 280px; float: right; margin-top: 10px; }
        .totals td { padding: 4px 8px; font-size: 12px; }
        .totals .label { color: #6b7280; }
        .totals .grand { font-size: 16px; font-weight: bold; color: #0ea5e9; border-top: 2px solid #0ea5e9; padding-top: 6px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; color: #fff; }
        .status-paid { background: #10b981; }
        .status-issued { background: #0ea5e9; }
        .status-partial { background: #f59e0b; }
        .footer { margin-top: 60px; clear: both; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 10px; text-align: center; }
        .signature-row { margin-top: 50px; clear: both; }
        .signature-row .col { display: inline-block; width: 45%; vertical-align: top; }
        .signature-row .line { border-top: 1px solid #1f2937; margin-top: 50px; padding-top: 6px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="right">
            <div class="label">Receipt</div>
            <div class="invoice-no">{{ $invoice->invoice_number }}</div>
            @php
                $statusClass = match($invoice->status) {
                    'paid' => 'status-paid', 'partial' => 'status-partial', default => 'status-issued',
                };
            @endphp
            <span class="status-badge {{ $statusClass }}">{{ strtoupper($invoice->status) }}</span>
        </div>
        <h1 class="clinic-name">{{ $invoice->branch->name ?? config('app.name') }}</h1>
        <div class="clinic-meta">
            @if($invoice->branch->address ?? false){{ $invoice->branch->address }}<br>@endif
            @if($invoice->branch->phone ?? false)Tel: {{ $invoice->branch->phone }} · @endif
            @if($invoice->branch->email ?? false)Email: {{ $invoice->branch->email }}@endif
        </div>
    </div>

    <table class="meta-grid">
        <tr>
            <td width="50%">
                <div class="label">Bill To</div>
                <div class="value">{{ $invoice->patient->name }}</div>
                <div>{{ $invoice->patient->patient_id }}</div>
                @if($invoice->patient->phone)<div>{{ $invoice->patient->phone }}</div>@endif
                @if($invoice->patient->email)<div>{{ $invoice->patient->email }}</div>@endif
            </td>
            <td width="50%">
                <div class="label">Date Issued</div>
                <div class="value">{{ $invoice->created_at->format('d F Y') }}</div>
                @if($invoice->payment_type === 'panel' && $invoice->insurancePanel)
                    <div class="label" style="margin-top:8px">Panel</div>
                    <div class="value">{{ $invoice->insurancePanel->company_name }}</div>
                @endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right" style="width:60px">Qty</th>
                <th class="text-right" style="width:90px">Unit Price</th>
                <th class="text-right" style="width:100px">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">RM {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">RM {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="label">Subtotal</td><td class="text-right">RM {{ number_format($invoice->subtotal, 2) }}</td></tr>
        @if($invoice->tax > 0)<tr><td class="label">Tax</td><td class="text-right">RM {{ number_format($invoice->tax, 2) }}</td></tr>@endif
        @if($invoice->discount > 0)<tr><td class="label">Discount</td><td class="text-right">- RM {{ number_format($invoice->discount, 2) }}</td></tr>@endif
        <tr><td class="grand label">TOTAL</td><td class="grand text-right">RM {{ number_format($invoice->total, 2) }}</td></tr>
        @php $totalPaid = $invoice->payments->sum('amount'); $balance = $invoice->total - $totalPaid; @endphp
        <tr><td class="label">Paid</td><td class="text-right">RM {{ number_format($totalPaid, 2) }}</td></tr>
        @if($balance > 0)<tr><td class="label" style="color:#dc2626">Balance Due</td><td class="text-right" style="color:#dc2626;font-weight:bold">RM {{ number_format($balance, 2) }}</td></tr>@endif
    </table>

    @if($invoice->payments->count())
    <div style="clear:both;padding-top:20px">
        <div style="font-size:10px;color:#6b7280;text-transform:uppercase;margin-bottom:6px">Payment History</div>
        <table class="items">
            <thead><tr><th>Date</th><th>Method</th><th>Reference</th><th class="text-right">Amount</th></tr></thead>
            <tbody>
                @foreach($invoice->payments as $p)
                    <tr>
                        <td>{{ $p->paid_at?->format('d M Y h:i A') ?? '-' }}</td>
                        <td>{{ ucfirst($p->payment_method) }}</td>
                        <td>{{ $p->reference ?? '-' }}</td>
                        <td class="text-right">RM {{ number_format($p->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($invoice->notes)
        <div style="margin-top:18px;padding:12px;background:#fef3c7;border-left:3px solid #f59e0b;font-size:11px">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
    @endif

    <div class="signature-row">
        <div class="col">
            <div class="line">Patient / Authorized Person</div>
        </div>
        <div class="col" style="text-align:right">
            <div class="line">Authorized Officer</div>
        </div>
    </div>

    <div class="footer">
        Thank you for choosing {{ $invoice->branch->name ?? config('app.name') }}. This is a system-generated document.<br>
        Generated on {{ now()->format('d F Y h:i A') }}
    </div>
</body>
</html>
