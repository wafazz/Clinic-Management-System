<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Referral {{ $referral->referral_number }}</h4></x-slot>
    <div class="card mb-3"><div class="card-body">
        <p><strong>Patient:</strong> {{ $referral->patient->name }} ({{ $referral->patient->patient_id }})</p>
        <p><strong>Referring Doctor:</strong> {{ $referral->referringDoctor ? 'Dr. ' . $referral->referringDoctor->user->name : '-' }}</p>
        <p><strong>Referred To:</strong> {{ $referral->referred_to }}</p>
        <p><strong>Specialty:</strong> {{ $referral->specialty ?? '-' }}</p>
        <p><strong>Date:</strong> {{ $referral->referral_date->format('d M Y') }}</p>
        <p><strong>Urgency:</strong> <span class="badge badge-{{ $referral->urgency === 'emergency' ? 'danger' : ($referral->urgency === 'urgent' ? 'warning' : 'secondary') }}">{{ ucfirst($referral->urgency) }}</span></p>
        <p><strong>Reason:</strong> {{ $referral->reason }}</p>
        @if($referral->clinical_summary)<p><strong>Clinical Summary:</strong> {{ $referral->clinical_summary }}</p>@endif
    </div></div>
    <div class="card"><div class="card-body">
        <h5>Update Status</h5>
        <form method="POST" action="{{ route('referrals.update-status', $referral) }}" class="d-flex gap-2">
            @csrf @method('PATCH')
            <select name="status" class="form-control" style="max-width:200px">
                @foreach(['pending','sent','completed','cancelled'] as $s)<option value="{{ $s }}" {{ $referral->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <button class="btn btn-primary btn-sm">Update</button>
        </form>
    </div></div>
</x-app-layout>
