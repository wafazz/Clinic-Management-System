<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; text-align: right; }
        .totals .total-line { font-size: 18px; font-weight: bold; }
        .btn-print { background: #4f46e5; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; margin-bottom: 20px; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
    <button class="btn-print" onclick="window.print()">Print Invoice</button>

    <div class="header">
        <div>
            <h1>{{ $invoice->branch->name }}</h1>
            <p>{{ $invoice->branch->address }}</p>
            <p>{{ $invoice->branch->phone }} | {{ $invoice->branch->email }}</p>
        </div>
        <div style="text-align: right;">
            <h2>INVOICE</h4>
            <p><strong>{{ $invoice->invoice_number }}</strong></p>
            <p>Date: {{ $invoice->created_at->format('d M Y') }}</p>
        </div>
    </div>

    <div>
        <p><strong>Patient:</strong> {{ $invoice->patient->name }}</p>
        <p><strong>Patient ID:</strong> {{ $invoice->patient->patient_id }}</p>
        <p><strong>IC:</strong> {{ $invoice->patient->ic_number ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price (RM)</th>
                <th class="text-right">Total (RM)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p>Subtotal: RM {{ number_format($invoice->subtotal, 2) }}</p>
        @if($invoice->tax > 0)<p>Tax: RM {{ number_format($invoice->tax, 2) }}</p>@endif
        @if($invoice->discount > 0)<p>Discount: -RM {{ number_format($invoice->discount, 2) }}</p>@endif
        <p class="total-line">Total: RM {{ number_format($invoice->total, 2) }}</p>
        <p>Status: {{ ucfirst($invoice->status) }}</p>
    </div>

    <hr style="margin-top: 40px;">
    <p style="text-align: center; font-size: 12px; color: #666;">Thank you for choosing {{ $invoice->branch->name }}. Get well soon!</p>
</body>
</html>
