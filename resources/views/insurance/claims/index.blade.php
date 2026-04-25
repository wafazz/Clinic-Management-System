<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Insurance Claims</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('insurance-panels.index') }}" class="btn btn-secondary btn-sm">View Panels</a>
                <a href="{{ route('insurance-claims.create') }}" class="btn btn-primary btn-sm">New Claim</a>
            </div>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search claim#, GL#, patient..." class="form-control form-control-sm" style="max-width:250px" />
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['draft', 'submitted', 'approved', 'partial', 'rejected', 'paid'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="panel_id" class="form-control form-control-sm" style="max-width:180px">
                    <option value="">All Panels</option>
                    @foreach($panels as $panel)
                        <option value="{{ $panel->id }}" {{ request('panel_id') == $panel->id ? 'selected' : '' }}>{{ $panel->company_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Claim #</th>
                    <th >Patient</th>
                    <th >Panel</th>
                    <th >Invoice</th>
                    <th >Claim Amount</th>
                    <th >GL</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @php $statusColors = ['draft' => 'badge-secondary', 'submitted' => 'badge-info', 'approved' => 'badge-success', 'partial' => 'badge-warning', 'rejected' => 'badge-danger', 'paid' => 'badge-success']; @endphp
                    @forelse($claims as $claim)
                        <tr>
                            <td >{{ $claim->claim_number }}</td>
                            <td >{{ $claim->patient->name ?? '-' }}</td>
                            <td >{{ $claim->panel->company_name ?? '-' }}</td>
                            <td >
                                @if($claim->invoice)
                                    <a href="{{ route('invoices.show', $claim->invoice) }}" >{{ $claim->invoice->invoice_number }}</a>
                                @else - @endif
                            </td>
                            <td >RM {{ number_format($claim->claim_amount, 2) }}</td>
                            <td >
                                @if($claim->gl_number)
                                    <span class="text-xs">{{ $claim->gl_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td >
                                <span class="badge {{ $statusColors[$claim->status] ?? 'badge-secondary' }}">{{ ucfirst($claim->status) }}</span>
                            </td>
                            <td >
                                <a href="{{ route('insurance-claims.show', $claim) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                @if(!in_array($claim->status, ['approved', 'paid']))
                                    <form method="POST" action="{{ route('insurance-claims.destroy', $claim) }}" class="d-inline" onsubmit="return confirm('Delete this claim?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No insurance claims found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $claims->links() }}</div>
        </div>
    </div>
</x-app-layout>
