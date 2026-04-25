<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Treatment Plans</h4>
            <a href="{{ route('treatment-plans.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Plan</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Plan #</th><th>Patient</th><th>Doctor</th><th>Title</th><th>Progress</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($plans as $p)
                    <tr>
                        <td>{{ $p->plan_number }}</td>
                        <td>{{ $p->patient->name }}</td>
                        <td>Dr. {{ $p->doctor->user->name }}</td>
                        <td>{{ $p->title }}</td>
                        <td>{{ $p->completed_sessions }} / {{ $p->total_sessions }} sessions</td>
                        <td>
                            @php $colors = ['active'=>'badge-success','completed'=>'badge-info','cancelled'=>'badge-danger','on_hold'=>'badge-warning']; @endphp
                            <span class="badge {{ $colors[$p->status] }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('treatment-plans.show', $p) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if($p->status === 'active')
                                <form method="POST" action="{{ route('treatment-plans.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Cancel?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-cancel"></i></button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No plans.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $plans->links() }}</div>
    </div></div>
</x-app-layout>
