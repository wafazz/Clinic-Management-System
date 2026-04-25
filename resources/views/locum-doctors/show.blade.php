<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">{{ $locumDoctor->name }}</h4>
            <a href="{{ route('locum-doctors.edit', $locumDoctor) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
    </x-slot>

    <div class="row mb-4">
        <div class="card"><div class="card-body">
            <h3 class="card-title">Profile</h3>
            <dl class="text-sm">
                <div><dt class="text-muted">Email</dt><dd>{{ $locumDoctor->email ?? '-' }}</dd></div>
                <div><dt class="text-muted">Phone</dt><dd>{{ $locumDoctor->phone ?? '-' }}</dd></div>
                <div><dt class="text-muted">IC Number</dt><dd>{{ $locumDoctor->ic_number ?? '-' }}</dd></div>
                <div><dt class="text-muted">MMC Number</dt><dd>{{ $locumDoctor->mmc_number ?? '-' }}</dd></div>
                <div><dt class="text-muted">Specialization</dt><dd>{{ $locumDoctor->specialization ?? '-' }}</dd></div>
                <div><dt class="text-muted">Hourly Rate</dt><dd>RM {{ number_format($locumDoctor->hourly_rate, 2) }}</dd></div>
                <div><dt class="text-muted">Session Rate</dt><dd>RM {{ number_format($locumDoctor->session_rate, 2) }}</dd></div>
                <div><dt class="text-muted">Bank Details</dt><dd>{{ $locumDoctor->bank_details ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="card"><div class="card-body">
            <h3 class="card-title">Session History</h3>
            <table class="table table-hover">
                <thead><tr>
                    <th class="text-left py-2">Date</th><th class="text-left py-2">Branch</th><th class="text-left py-2">Pay</th><th class="text-left py-2">Paid</th>
                </tr></thead>
                <tbody>
                    @forelse($locumDoctor->sessions as $session)
                        <tr class="border-t">
                            <td class="py-2">{{ $session->session_date->format('d M Y') }}</td>
                            <td class="py-2">{{ $session->branch->name }}</td>
                            <td class="py-2">RM {{ number_format($session->total_pay, 2) }}</td>
                            <td class="py-2">
                                <span class="badge {{ $session->is_paid ? 'badge-success' : 'badge-danger' }}">{{ $session->is_paid ? 'Yes' : 'No' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-2 text-muted">No sessions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
