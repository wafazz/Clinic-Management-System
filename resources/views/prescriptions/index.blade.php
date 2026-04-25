<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Prescriptions</h4>
            <a href="{{ route('prescriptions.create') }}" class="btn btn-primary btn-sm">New Prescription</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search patient..." class="form-control form-control-sm" style="max-width:250px" />
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['draft', 'dispensed', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >ID</th>
                    <th >Patient</th>
                    <th >Doctor</th>
                    <th >Items</th>
                    <th >Status</th>
                    <th >Date</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($prescriptions as $rx)
                        <tr>
                            <td >#{{ $rx->id }}</td>
                            <td >{{ $rx->patient->name }}</td>
                            <td >{{ $rx->doctor->user->name ?? '-' }}</td>
                            <td >{{ $rx->items->count() }} items</td>
                            <td >
                                @php $colors = ['draft' => 'badge-warning', 'dispensed' => 'badge-success', 'cancelled' => 'badge-danger']; @endphp
                                <span class="badge {{ $colors[$rx->status] ?? 'badge-secondary' }}">{{ ucfirst($rx->status) }}</span>
                            </td>
                            <td >{{ $rx->created_at->format('d M Y') }}</td>
                            <td >
                                <a href="{{ route('prescriptions.show', $rx) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                @if($rx->status === 'draft')
                                    <form method="POST" action="{{ route('prescriptions.dispense', $rx) }}" class="d-inline" onsubmit="return confirm('Dispense this prescription? Stock will be deducted.')">
                                        @csrf @method('PATCH')
                                        <button class="text-success hover:underline">Dispense</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No prescriptions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $prescriptions->links() }}</div>
        </div>
    </div>
</x-app-layout>
