@extends('portal.layout')

@section('content')
    <h1 class="text-2xl font-bold mb-6">My Invoices</h1>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="table table-striped table-hover">
            <thead ><tr>
                <th >Invoice #</th>
                <th >Date</th>
                <th >Total (RM)</th>
                <th >Paid (RM)</th>
                <th >Status</th>
                <th >Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($invoices as $inv)
                    @php $paid = $inv->payments->sum('amount'); @endphp
                    <tr>
                        <td >{{ $inv->invoice_number }}</td>
                        <td >{{ $inv->created_at->format('d M Y') }}</td>
                        <td >{{ number_format($inv->total, 2) }}</td>
                        <td >{{ number_format($paid, 2) }}</td>
                        <td >
                            <span class="badge {{ $inv->status === 'paid' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($inv->status) }}</span>
                        </td>
                        <td >
                            <a href="{{ route('portal.invoices.show', $inv->id) }}" class="text-info hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No invoices found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $invoices->links() }}</div>
    </div>
@endsection
