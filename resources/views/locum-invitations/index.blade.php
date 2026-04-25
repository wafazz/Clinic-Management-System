<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-email-fast text-primary mr-2"></i>Locum Invitations</h4>
                <small class="text-muted">Invite locum doctors to access consultation + treatment plan features for a specific period.</small>
            </div>
            <a href="{{ route('locum-invitations.create') }}" class="btn btn-primary btn-sm"><i class="mdi mdi-plus mr-1"></i>New Invitation</a>
        </div>
    </x-slot>

    <div class="card"><div class="card-body">
        <form method="GET" class="d-flex mb-3" style="gap:8px">
            <select name="status" class="form-control form-control-sm" style="max-width:160px">
                <option value="">All status</option>
                @foreach(['pending','accepted','declined','revoked','expired'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select name="locum_doctor_id" class="form-control form-control-sm" style="max-width:240px">
                <option value="">All locums</option>
                @foreach($locumDoctors as $ld)
                    <option value="{{ $ld->id }}" {{ request('locum_doctor_id') == $ld->id ? 'selected' : '' }}>{{ $ld->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-secondary btn-sm">Filter</button>
            <a href="{{ route('locum-invitations.index') }}" class="btn btn-light btn-sm">Clear</a>
        </form>

        <table class="table">
            <thead><tr>
                <th>Locum</th><th>Branch</th><th>Period</th><th>Permissions</th><th>Status</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($invitations as $inv)
                    <tr>
                        <td>
                            <strong>{{ $inv->locumDoctor->name }}</strong>
                            <br><small class="text-muted">{{ $inv->locumDoctor->specialization ?? '—' }}</small>
                        </td>
                        <td>{{ $inv->branch->name }}</td>
                        <td><small>
                            {{ $inv->valid_from->format('d M Y h:i A') }}<br>
                            <span class="text-muted">→ {{ $inv->valid_to->format('d M Y h:i A') }}</span>
                            @if($inv->isActive())
                                <br><span class="badge badge-success"><i class="mdi mdi-circle"></i> Active now</span>
                            @endif
                        </small></td>
                        <td>
                            @if($inv->can_consultation)<span class="badge badge-info"><i class="mdi mdi-stethoscope"></i> Consultation</span>@endif
                            @if($inv->can_treatment_plan)
                                <span class="badge badge-info"><i class="mdi mdi-clipboard-list"></i> Treatment Plan</span>
                                @if($inv->treatment_plan_requires_approval)<small class="text-muted d-block">requires approval</small>@endif
                            @endif
                        </td>
                        <td>
                            @php $colors = ['pending'=>'warning','accepted'=>'success','declined'=>'danger','revoked'=>'dark','expired'=>'secondary']; @endphp
                            <span class="badge badge-{{ $colors[$inv->status] }}">{{ ucfirst($inv->status) }}</span>
                            @if($inv->status === 'accepted')
                                <small class="text-muted d-block mt-1">accepted {{ $inv->accepted_at?->diffForHumans() }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('locum-invitations.show', $inv) }}" class="btn btn-outline-info btn-sm py-1 px-2"><i class="mdi mdi-eye"></i></a>
                            @if(in_array($inv->status, ['pending', 'accepted']))
                                <form method="POST" action="{{ route('locum-invitations.revoke', $inv) }}" class="d-inline" onsubmit="return confirm('Revoke this invitation? Locum will lose access immediately.')">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-outline-warning btn-sm py-1 px-2" title="Revoke"><i class="mdi mdi-cancel"></i></button>
                                </form>
                            @endif
                            @if(!$inv->isActive())
                                <form method="POST" action="{{ route('locum-invitations.destroy', $inv) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4"><i class="mdi mdi-email-open" style="font-size:32px;opacity:0.3"></i><br><small>No invitations yet. Create one to give a locum clinical access.</small></td></tr>
                @endforelse
            </tbody>
        </table>
        <div>{{ $invitations->links() }}</div>
    </div></div>
</x-app-layout>
