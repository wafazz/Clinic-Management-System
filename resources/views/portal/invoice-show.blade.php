@extends('portal.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="text-2xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
        <a href="{{ route('portal.invoices') }}" class="text-info text-sm">Back to Invoices</a>
    </div>

    <div class="card"><div class="card-body">
        <div class="row mb-3 text-sm">
            <div><span class="text-muted">Date</span><p class="font-medium">{{ $invoice->created_at->format('d M Y') }}</p></div>
            <div><span class="text-muted">Branch</span><p class="font-medium">{{ $invoice->branch->name ?? '-' }}</p></div>
            <div><span class="text-muted">Total</span><p class="font-bold text-lg">RM {{ number_format($invoice->total, 2) }}</p></div>
            <div><span class="text-muted">Status</span>
                <p><span class="badge {{ $invoice->status === 'paid' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($invoice->status) }}</span></p>
            </div>
        </div>

        <h3 class="font-weight-bold mb-2">Items</h3>
        <table class="table table-hover text-sm mb-4">
            <thead ><tr>
                <th >Description</th>
                <th >Qty</th>
                <th >Price (RM)</th>
                <th >Total (RM)</th>
            </tr></thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr class="border-t">
                        <td >{{ $item->description }}</td>
                        <td >{{ $item->quantity }}</td>
                        <td >{{ number_format($item->unit_price, 2) }}</td>
                        <td >{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t font-medium">
                <tr><td colspan="3" class="px-3 py-2 text-right">Subtotal</td><td >{{ number_format($invoice->subtotal, 2) }}</td></tr>
                @if($invoice->tax > 0)<tr><td colspan="3" class="px-3 py-2 text-right">Tax</td><td >{{ number_format($invoice->tax, 2) }}</td></tr>@endif
                @if($invoice->discount > 0)<tr><td colspan="3" class="px-3 py-2 text-right">Discount</td><td >-{{ number_format($invoice->discount, 2) }}</td></tr>@endif
                <tr class="text-lg"><td colspan="3" class="px-3 py-2 text-right">Total</td><td >RM {{ number_format($invoice->total, 2) }}</td></tr>
            </tfoot>
        </table>

        @if($invoice->payments->count())
            <h3 class="font-weight-bold mb-2">Payments</h3>
            <table class="table table-hover">
                <thead ><tr>
                    <th >Date</th>
                    <th >Method</th>
                    <th >Amount (RM)</th>
                </tr></thead>
                <tbody>
                    @foreach($invoice->payments as $pay)
                        <tr class="border-t">
                            <td >{{ $pay->payment_date->format('d M Y') }}</td>
                            <td >{{ ucfirst(str_replace('_', ' ', $pay->method)) }}</td>
                            <td >{{ number_format($pay->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div></div>
@endsection
