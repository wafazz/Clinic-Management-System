<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Invitation #{{ $locumInvitation->id }}</h4>
            <a href="{{ route('locum-invitations.index') }}" class="btn btn-light btn-sm">Back</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <dl class="detail-list">
                <div><dt>Locum</dt><dd><strong>{{ $locumInvitation->locumDoctor->name }}</strong> — {{ $locumInvitation->locumDoctor->specialization ?? '—' }}</dd></div>
                <div><dt>Branch</dt><dd>{{ $locumInvitation->branch->name }}</dd></div>
                <div><dt>Valid From</dt><dd>{{ $locumInvitation->valid_from->format('d F Y, h:i A') }}</dd></div>
                <div><dt>Valid Until</dt><dd>{{ $locumInvitation->valid_to->format('d F Y, h:i A') }}</dd></div>
                <div><dt>Status</dt><dd>
                    @php $colors = ['pending'=>'warning','accepted'=>'success','declined'=>'danger','revoked'=>'dark','expired'=>'secondary']; @endphp
                    <span class="badge badge-{{ $colors[$locumInvitation->status] }}">{{ ucfirst($locumInvitation->status) }}</span>
                    @if($locumInvitation->isActive())<span class="badge badge-success ml-1"><i class="mdi mdi-circle"></i> Active right now</span>@endif
                </dd></div>
                <div><dt>Consultation Access</dt><dd>{!! $locumInvitation->can_consultation ? '<span class="text-success"><i class="mdi mdi-check-circle"></i> Allowed</span>' : '<span class="text-muted">Not allowed</span>' !!}</dd></div>
                <div><dt>Treatment Plan Access</dt><dd>{!! $locumInvitation->can_treatment_plan ? '<span class="text-success"><i class="mdi mdi-check-circle"></i> Allowed</span>' : '<span class="text-muted">Not allowed</span>' !!}</dd></div>
                <div><dt>Plans Require Approval</dt><dd>{{ $locumInvitation->treatment_plan_requires_approval ? 'Yes' : 'No' }}</dd></div>
                @if($locumInvitation->accepted_at)<div><dt>Accepted</dt><dd>{{ $locumInvitation->accepted_at->format('d M Y h:i A') }} ({{ $locumInvitation->accepted_at->diffForHumans() }})</dd></div>@endif
                @if($locumInvitation->revoked_at)<div><dt>Revoked</dt><dd>{{ $locumInvitation->revoked_at->format('d M Y h:i A') }}</dd></div>@endif
                <div><dt>Created By</dt><dd>{{ $locumInvitation->createdBy->name ?? '—' }}</dd></div>
                @if($locumInvitation->notes)<div><dt>Notes</dt><dd>{{ $locumInvitation->notes }}</dd></div>@endif
            </dl>

            @if(in_array($locumInvitation->status, ['pending', 'accepted']))
                <hr>
                <form method="POST" action="{{ route('locum-invitations.revoke', $locumInvitation) }}" onsubmit="return confirm('Revoke this invitation?')">
                    @csrf @method('PATCH')
                    <button class="btn btn-outline-warning"><i class="mdi mdi-cancel mr-1"></i>Revoke Access</button>
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
