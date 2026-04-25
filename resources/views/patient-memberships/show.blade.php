<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Membership {{ $patientMembership->membership_number }}</h4></x-slot>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-body">
                <h5>{{ $patientMembership->patient->name }}</h5>
                <p>{{ $patientMembership->tier->name }} — RM {{ number_format($patientMembership->tier->price, 2) }} / {{ $patientMembership->tier->billing_cycle }}</p>
                <p><strong>Status:</strong> <span class="badge badge-{{ $patientMembership->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($patientMembership->status) }}</span></p>
                <p><strong>Period:</strong> {{ $patientMembership->start_date->format('d M Y') }} → {{ $patientMembership->end_date?->format('d M Y') ?? '∞' }}</p>
                <p><strong>Savings:</strong> RM {{ number_format($patientMembership->total_savings, 2) }}</p>
                <p><strong>Free Used:</strong> Cons {{ $patientMembership->free_consultations_used }} / {{ $patientMembership->tier->free_consultations_per_year }}, Lab {{ $patientMembership->free_lab_tests_used }} / {{ $patientMembership->tier->free_lab_tests_per_year }}</p>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-body">
                <h5>Family Members ({{ $patientMembership->familyMembers->count() }} / {{ $patientMembership->tier->max_family_members }})</h5>
                @forelse($patientMembership->familyMembers as $fm)
                    <p class="mb-1">{{ $fm->patient->name }} <small class="text-muted">({{ ucfirst($fm->relationship) }})</small></p>
                @empty
                    <p class="text-muted small">No family members added yet.</p>
                @endforelse
            </div></div>
        </div>
    </div>

    <div class="card"><div class="card-body">
        <h5>Usage Logs</h5>
        <table class="table"><thead><tr><th>Date</th><th>Type</th><th>Description</th><th>Savings</th></tr></thead><tbody>
            @forelse($patientMembership->usageLogs->take(20) as $log)
                <tr><td><small>{{ $log->used_at?->format('d M Y h:i A') }}</small></td><td>{{ ucfirst(str_replace('_', ' ', $log->usage_type)) }}</td><td>{{ $log->description }}</td><td>RM {{ number_format($log->savings_amount, 2) }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">No usage logged.</td></tr>
            @endforelse
        </tbody></table>
    </div></div>
</x-app-layout>
