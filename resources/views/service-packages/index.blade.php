<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Service Packages</h4>
            <a href="{{ route('service-packages.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Package</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Type</th><th>Price</th><th>Cycle</th><th>Visits</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($packages as $p)
                    <tr>
                        <td>{{ $p->name }}<br><small class="text-muted">{{ \Illuminate\Support\Str::limit($p->description, 50) }}</small></td>
                        <td><span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $p->type)) }}</span></td>
                        <td>RM {{ number_format($p->price, 2) }}</td>
                        <td>{{ ucfirst($p->billing_cycle) }}</td>
                        <td>{{ $p->max_visits ?? '∞' }}</td>
                        <td>@if($p->is_active)<span class="badge badge-success">Yes</span>@else<span class="badge badge-secondary">No</span>@endif</td>
                        <td>
                            <a href="{{ route('service-packages.show', $p) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            <form method="POST" action="{{ route('service-packages.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Deactivate?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-power"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No packages.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $packages->links() }}</div>
    </div></div>
</x-app-layout>
