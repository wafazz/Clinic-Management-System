<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Sales Leads</h4>
            <a href="{{ route('leads.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Lead</a>
        </div>
    </x-slot>

    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-2"><div class="card bg-primary text-white"><div class="card-body py-3"><p class="mb-0 small">Total</p><h3 class="mb-0">{{ $stats['total'] }}</h3></div></div></div>
        <div class="col-md-3 col-6 mb-2"><div class="card bg-info text-white"><div class="card-body py-3"><p class="mb-0 small">New</p><h3 class="mb-0">{{ $stats['new'] }}</h3></div></div></div>
        <div class="col-md-3 col-6 mb-2"><div class="card bg-warning text-white"><div class="card-body py-3"><p class="mb-0 small">Follow-up</p><h3 class="mb-0">{{ $stats['follow_up'] }}</h3></div></div></div>
        <div class="col-md-3 col-6 mb-2"><div class="card bg-success text-white"><div class="card-body py-3"><p class="mb-0 small">Converted</p><h3 class="mb-0">{{ $stats['success'] }}</h3></div></div></div>
    </div>

    <div class="card"><div class="card-body">
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Phone..." class="form-control form-control-sm" style="max-width:250px" />
            <select name="status" class="form-control form-control-sm" style="max-width:200px">
                <option value="">All Status</option>
                @foreach(['new_lead','contacted','followup_1','followup_2','followup_3','appointment_booked','success','reject','kiv','no_answer','wrong_number'] as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>@endforeach
            </select>
            <button class="btn btn-secondary btn-sm">Filter</button>
        </form>

        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Phone</th><th>Source</th><th>Interest</th><th>Status</th><th>Next Follow-up</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($leads as $l)
                    <tr>
                        <td>{{ $l->name }}<br><small class="text-muted">{{ $l->assignedTo?->name ?? '-' }}</small></td>
                        <td>{{ $l->phone }}</td>
                        <td>{{ $l->source ?? '-' }}</td>
                        <td>{{ $l->service_interest ?? '-' }}</td>
                        <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $l->status)) }}</span></td>
                        <td><small>{{ $l->next_followup_at?->format('d M h:i A') ?? '-' }}</small></td>
                        <td>
                            <a href="{{ route('leads.show', $l) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if(!$l->patient_id)
                                <form method="POST" action="{{ route('leads.convert', $l) }}" class="d-inline" onsubmit="return confirm('Convert lead to patient?')">@csrf<button class="btn btn-outline-success btn-sm py-1 px-2" title="Convert"><i class="mdi mdi-account-plus"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No leads.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $leads->links() }}</div>
    </div></div>
</x-app-layout>
