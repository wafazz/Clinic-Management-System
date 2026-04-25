<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Suppliers</h4>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Supplier</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <form method="GET" class="mb-3 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supplier..." class="form-control form-control-sm" style="max-width:300px" />
            <button class="btn btn-secondary btn-sm">Filter</button>
        </form>
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($suppliers as $s)
                    <tr>
                        <td>{{ $s->name }}</td>
                        <td>{{ $s->contact_person ?? '-' }}</td>
                        <td>{{ $s->phone }}</td>
                        <td>{{ $s->email ?? '-' }}</td>
                        <td>@if($s->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif</td>
                        <td>
                            <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-outline-primary btn-sm py-1 px-2"><i class="mdi mdi-pencil"></i></a>
                            <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No suppliers.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $suppliers->links() }}</div>
    </div></div>
</x-app-layout>
