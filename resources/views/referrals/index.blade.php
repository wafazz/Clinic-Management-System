<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Referrals</h4>
            <a href="{{ route('referrals.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Referral</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <table class="table table-striped">
            <thead><tr><th>Ref #</th><th>Patient</th><th>Referred To</th><th>Specialty</th><th>Date</th><th>Urgency</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($referrals as $r)
                    <tr>
                        <td>{{ $r->referral_number }}</td>
                        <td>{{ $r->patient->name }}</td>
                        <td>{{ $r->referred_to }}</td>
                        <td>{{ $r->specialty ?? '-' }}</td>
                        <td>{{ $r->referral_date->format('d M Y') }}</td>
                        <td>
                            @php $u = ['routine'=>'badge-secondary','urgent'=>'badge-warning','emergency'=>'badge-danger']; @endphp
                            <span class="badge {{ $u[$r->urgency] }}">{{ ucfirst($r->urgency) }}</span>
                        </td>
                        <td>
                            @php $s = ['pending'=>'badge-warning','sent'=>'badge-info','completed'=>'badge-success','cancelled'=>'badge-danger']; @endphp
                            <span class="badge {{ $s[$r->status] }}">{{ ucfirst($r->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('referrals.show', $r) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            <form method="POST" action="{{ route('referrals.destroy', $r) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No referrals.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $referrals->links() }}</div>
    </div></div>
</x-app-layout>
