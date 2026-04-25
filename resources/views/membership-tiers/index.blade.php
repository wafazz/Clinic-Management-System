<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Membership Tiers</h4>
            <a href="{{ route('membership-tiers.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Tier</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Name</th><th>Price</th><th>Cycle</th><th>Discounts</th><th>Free/Yr</th><th>Family</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($tiers as $t)
                    <tr>
                        <td>{{ $t->name }}</td>
                        <td>RM {{ number_format($t->price, 2) }}</td>
                        <td>{{ ucfirst($t->billing_cycle) }}</td>
                        <td><small>Cons: {{ $t->discount_consultation }}% / Med: {{ $t->discount_medicine }}% / Lab: {{ $t->discount_lab }}%</small></td>
                        <td><small>Cons: {{ $t->free_consultations_per_year }} / Lab: {{ $t->free_lab_tests_per_year }}</small></td>
                        <td>{{ $t->max_family_members }}</td>
                        <td>@if($t->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif</td>
                        <td>
                            <a href="{{ route('membership-tiers.edit', $t) }}" class="btn btn-outline-primary btn-sm py-1 px-2"><i class="mdi mdi-pencil"></i></a>
                            <form method="POST" action="{{ route('membership-tiers.destroy', $t) }}" class="d-inline" onsubmit="return confirm('Delete tier?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No tiers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $tiers->links() }}</div>
    </div></div>
</x-app-layout>
