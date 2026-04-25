<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Payment {{ $locumPayment->payment_number }}</h4></x-slot>
    <div class="card mb-3"><div class="card-body">
        <p><strong>Locum:</strong> {{ $locumPayment->locumDoctor->name }}</p>
        <p><strong>Period:</strong> {{ $locumPayment->period_start->format('d M Y') }} → {{ $locumPayment->period_end->format('d M Y') }}</p>
        <p><strong>Sessions:</strong> {{ $locumPayment->total_sessions }}</p>
        <p><strong>Gross:</strong> RM {{ number_format($locumPayment->gross_amount, 2) }}</p>
        <p><strong>Deductions:</strong> RM {{ number_format($locumPayment->deductions, 2) }}</p>
        <h4 class="text-success"><strong>Net: RM {{ number_format($locumPayment->net_amount, 2) }}</strong></h4>
        <p><strong>Method:</strong> {{ ucfirst(str_replace('_', ' ', $locumPayment->payment_method)) }}</p>
        <p><strong>Status:</strong> <span class="badge badge-{{ $locumPayment->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($locumPayment->status) }}</span></p>
    </div></div>

    <div class="card"><div class="card-body">
        <h5>Session Items</h5>
        <table class="table"><thead><tr><th>Date</th><th>Rate</th><th>Subtotal</th></tr></thead><tbody>
            @foreach($locumPayment->items as $i)
                <tr><td>{{ $i->session_date->format('d M Y') }}</td><td>RM {{ number_format($i->rate_amount, 2) }}</td><td>RM {{ number_format($i->subtotal, 2) }}</td></tr>
            @endforeach
        </tbody></table>
    </div></div>
</x-app-layout>
