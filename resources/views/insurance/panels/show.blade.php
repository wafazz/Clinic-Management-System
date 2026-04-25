<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">{{ $insurancePanel->company_name }}</h4>
            <a href="{{ route('insurance-panels.edit', $insurancePanel) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
    </x-slot>

    {{-- Stats --}}
    <div class="row mb-4">
        <div class="card"><div class="card-body text-center">
            <p class="text-2xl font-bold text-primary">{{ $totalClaims }}</p>
            <p class="text-xs text-muted">Total Claims</p>
        </div>
        <div class="card"><div class="card-body text-center">
            <p class="text-2xl font-bold text-success">RM {{ number_format($totalClaimAmount, 2) }}</p>
            <p class="text-xs text-muted">Total Claimed</p>
        </div>
        <div class="card"><div class="card-body text-center">
            <p class="text-2xl font-bold text-warning">{{ $pendingClaims }}</p>
            <p class="text-xs text-muted">Pending Claims</p>
        </div>
        <div class="card"><div class="card-body text-center">
            <p class="text-2xl font-bold text-danger">RM {{ number_format($unpaidAmount, 2) }}</p>
            <p class="text-xs text-muted">Unpaid Amount</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="card"><div class="card-body">
            <h3 class="card-title">Panel Details</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Type</dt>
                    @php $typeColors = ['corporate' => 'badge-info', 'insurance' => 'badge-primary', 'tpa' => 'badge-warning', 'government' => 'badge-success']; @endphp
                    <dd><span class="badge {{ $typeColors[$insurancePanel->type] ?? 'badge-secondary' }}">{{ ucfirst($insurancePanel->type) }}</span></dd>
                </div>
                <div><dt class="text-muted">Contact Person</dt><dd>{{ $insurancePanel->contact_person ?? '-' }}</dd></div>
                <div><dt class="text-muted">Phone</dt><dd>{{ $insurancePanel->phone ?? '-' }}</dd></div>
                <div><dt class="text-muted">Email</dt><dd>{{ $insurancePanel->email ?? '-' }}</dd></div>
                <div><dt class="text-muted">Address</dt><dd>{{ $insurancePanel->address ?? '-' }}</dd></div>
                <div><dt class="text-muted">Credit Terms</dt><dd>{{ $insurancePanel->credit_terms }} days</dd></div>
                <div><dt class="text-muted">Per Visit Limit</dt><dd>{{ $insurancePanel->consultation_limit ? 'RM ' . number_format($insurancePanel->consultation_limit, 2) : 'Unlimited' }}</dd></div>
                <div><dt class="text-muted">Annual Limit</dt><dd>{{ $insurancePanel->annual_limit ? 'RM ' . number_format($insurancePanel->annual_limit, 2) : 'Unlimited' }}</dd></div>
                <div><dt class="text-muted">GL Required</dt><dd>{{ $insurancePanel->requires_gl ? 'Yes' : 'No' }}</dd></div>
                <div><dt class="text-muted">Covered Services</dt><dd>{{ $insurancePanel->covered_services ?? '-' }}</dd></div>
                <div><dt class="text-muted">Exclusions</dt><dd>{{ $insurancePanel->exclusions ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="card"><div class="card-body">
            <h3 class="card-title">Registered Members</h3>
            <table class="table table-hover">
                <thead><tr>
                    <th class="text-left py-2">Patient</th>
                    <th class="text-left py-2">Member ID</th>
                    <th class="text-left py-2">Expiry</th>
                    <th class="text-left py-2">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($insurancePanel->patientInsurances as $pi)
                        <tr class="border-t">
                            <td class="py-2"><a href="{{ route('patients.show', $pi->patient) }}" >{{ $pi->patient->name }}</a></td>
                            <td class="py-2">{{ $pi->member_id ?? '-' }}</td>
                            <td class="py-2 {{ $pi->isExpired() ? 'text-danger' : '' }}">{{ $pi->expiry_date?->format('d M Y') ?? '-' }}</td>
                            <td class="py-2">
                                @php $sColors = ['active' => 'badge-success', 'expired' => 'badge-danger', 'suspended' => 'badge-warning']; @endphp
                                <span class="badge {{ $sColors[$pi->status] ?? 'badge-secondary' }}">{{ ucfirst($pi->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-2 text-muted">No members registered.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('insurance-panels.index') }}" class="btn btn-light btn-sm">Back</a>
</x-app-layout>
