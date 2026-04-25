<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Invoice {{ $invoice->invoice_number }}</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-success btn-sm">Print</a>
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary btn-sm">Edit</a>
            </div>
        </div>
    </x-slot>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <p class="text-sm text-muted mb-0">Patient</p>
                            <p class="font-weight-bold">{{ $invoice->patient->name }}</p>
                            <p class="text-sm text-muted">{{ $invoice->patient->patient_id }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-muted mb-0">Branch</p>
                            <p class="font-weight-bold">{{ $invoice->branch->name }}</p>
                            <p class="text-sm text-muted">{{ $invoice->created_at->format('d M Y') }}</p>
                        </div>
                    </div>

                    <table class="table table-hover mb-4">
                        <thead><tr>
                            <th>Description</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Total</th>
                        </tr></thead>
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

                    <div class="border-top pt-3 text-right">
                        <p class="text-sm mb-1">Subtotal: <span class="font-weight-bold">RM {{ number_format($invoice->subtotal, 2) }}</span></p>
                        @if($invoice->tax > 0)<p class="text-sm mb-1">Tax: RM {{ number_format($invoice->tax, 2) }}</p>@endif
                        @if($invoice->discount > 0)<p class="text-sm mb-1">Discount: -RM {{ number_format($invoice->discount, 2) }}</p>@endif
                        <p class="h5 font-weight-bold">Total: RM {{ number_format($invoice->total, 2) }}</p>
                        <p class="text-sm mb-1">Paid: <span class="text-success font-weight-bold">RM {{ number_format($invoice->total_paid, 2) }}</span></p>
                        <p class="text-sm">Balance: <span class="text-danger font-weight-bold">RM {{ number_format($invoice->balance_due, 2) }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Status</h5>
                    @php $statusColors = ['paid' => 'badge-success', 'partial' => 'badge-warning', 'issued' => 'badge-info', 'draft' => 'badge-secondary']; @endphp
                    <span class="badge {{ $statusColors[$invoice->status] ?? 'badge-secondary' }}">{{ ucfirst($invoice->status) }}</span>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Payments</h5>
                    @foreach($invoice->payments as $payment)
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <p class="font-weight-bold mb-0">RM {{ number_format($payment->amount, 2) }}</p>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $payment->method)) }} - {{ $payment->payment_date->format('d/m/Y') }}</small>
                            </div>
                            <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Remove payment?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-link text-danger btn-sm p-0">Remove</button>
                            </form>
                        </div>
                    @endforeach

                    @if($invoice->balance_due > 0)
                        <h6 class="font-weight-bold mt-3 mb-2">Record Payment</h6>
                        <form method="POST" action="{{ route('payments.store', $invoice) }}">
                            @csrf
                            <div class="form-group">
                                <input type="number" step="0.01" name="amount" value="{{ $invoice->balance_due }}" required class="form-control form-control-sm" placeholder="Amount" />
                            </div>
                            <div class="form-group">
                                <select name="method" required class="form-control form-control-sm">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="e_wallet">E-Wallet</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="reference" class="form-control form-control-sm" placeholder="Reference (optional)" />
                            </div>
                            <div class="form-group">
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="form-control form-control-sm" />
                            </div>
                            <button type="submit" class="btn btn-success btn-sm btn-block">Record Payment</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
