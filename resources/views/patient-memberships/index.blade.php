<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Patient Memberships</h4>
            <a href="{{ route('patient-memberships.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Membership</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Patient name..." class="form-control form-control-sm" style="max-width:250px" />
            <select name="status" class="form-control form-control-sm" style="max-width:150px">
                <option value="">All</option>
                @foreach(['active','expired','cancelled','suspended'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="btn btn-secondary btn-sm">Filter</button>
        </form>

        <table class="table table-striped">
            <thead><tr><th>Number</th><th>Patient</th><th>Tier</th><th>Period</th><th>Savings</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($memberships as $m)
                    <tr>
                        <td>{{ $m->membership_number }}</td>
                        <td>{{ $m->patient->name }}</td>
                        <td>{{ $m->tier->name }}</td>
                        <td><small>{{ $m->start_date->format('d M Y') }} → {{ $m->end_date?->format('d M Y') ?? '∞' }}</small></td>
                        <td>RM {{ number_format($m->total_savings, 2) }}</td>
                        <td>
                            @php $colors = ['active'=>'badge-success','expired'=>'badge-warning','cancelled'=>'badge-danger','suspended'=>'badge-secondary']; @endphp
                            <span class="badge {{ $colors[$m->status] }}">{{ ucfirst($m->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('patient-memberships.show', $m) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if($m->status === 'active')
                                <form method="POST" action="{{ route('patient-memberships.destroy', $m) }}" class="d-inline" onsubmit="return confirm('Cancel membership?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2" title="Cancel"><i class="mdi mdi-cancel"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No memberships.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $memberships->links() }}</div>
    </div></div>
</x-app-layout>
