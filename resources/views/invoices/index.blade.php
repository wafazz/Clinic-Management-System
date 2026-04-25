<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Invoices</h4>
            <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">Create Invoice</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['draft','issued','paid','partial','cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Invoice #</th>
                    <th >Patient</th>
                    <th >Date</th>
                    <th >Total (RM)</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($invoices as $invoice)
                        <tr>
                            <td >{{ $invoice->invoice_number }}</td>
                            <td >{{ $invoice->patient->name }}</td>
                            <td >{{ $invoice->created_at->format('d M Y') }}</td>
                            <td >{{ number_format($invoice->total, 2) }}</td>
                            <td >
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($invoice->status === 'paid') badge-success
                                    @elseif($invoice->status === 'partial') badge-warning
                                    @elseif($invoice->status === 'issued') badge-info
                                    @else bg-light 
                                @endif">{{ ucfirst($invoice->status) }}</span>
                            </td>
                            <td >
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('invoices.print', $invoice) }}" class="text-success hover:underline">Print</a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $invoices->links() }}</div>
        </div>
    </div>
</x-app-layout>
