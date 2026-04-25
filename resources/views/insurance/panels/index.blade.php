<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Insurance Panels</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('insurance-claims.index') }}" class="btn btn-success btn-sm">View Claims</a>
                <a href="{{ route('insurance-panels.create') }}" class="btn btn-primary btn-sm">Add Panel</a>
            </div>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search panels..." class="form-control form-control-sm" style="max-width:250px" />
                <select name="type" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Types</option>
                    @foreach(['corporate', 'insurance', 'tpa', 'government'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Company</th>
                    <th >Type</th>
                    <th >Contact</th>
                    <th >Credit Terms</th>
                    <th >Limits</th>
                    <th >GL Required</th>
                    <th >Members</th>
                    <th >Status</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($panels as $panel)
                        <tr>
                            <td >{{ $panel->company_name }}</td>
                            <td >
                                @php $typeColors = ['corporate' => 'badge-info', 'insurance' => 'badge-primary', 'tpa' => 'badge-warning', 'government' => 'badge-success']; @endphp
                                <span class="badge {{ $typeColors[$panel->type] ?? 'badge-secondary' }}">{{ ucfirst($panel->type) }}</span>
                            </td>
                            <td >{{ $panel->contact_person ?? '-' }}<br><span class="text-muted">{{ $panel->phone }}</span></td>
                            <td >{{ $panel->credit_terms }} days</td>
                            <td >
                                @if($panel->consultation_limit) Per visit: RM {{ number_format($panel->consultation_limit, 2) }}<br> @endif
                                @if($panel->annual_limit) Annual: RM {{ number_format($panel->annual_limit, 2) }} @endif
                                @if(!$panel->consultation_limit && !$panel->annual_limit) - @endif
                            </td>
                            <td >
                                @if($panel->requires_gl)
                                    <span class="badge badge-warning">Yes</span>
                                @else
                                    <span class="text-muted">No</span>
                                @endif
                            </td>
                            <td >{{ $panel->patient_insurances_count }}</td>
                            <td >
                                <span class="badge {{ $panel->is_active ? 'badge-success' : 'badge-danger' }}">{{ $panel->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td >
                                <a href="{{ route('insurance-panels.show', $panel) }}" class="btn btn-outline-info btn-sm py-1 px-2">View</a>
                                <a href="{{ route('insurance-panels.edit', $panel) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">No insurance panels found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $panels->links() }}</div>
        </div>
    </div>
</x-app-layout>
