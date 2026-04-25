<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Locum Session Details</h4></x-slot>

    <div class="card"><div class="card-body">
        <dl class="text-sm">
            <div><dt class="text-muted">Locum Doctor</dt><dd class="font-medium text-lg">{{ $locumSession->locumDoctor->name }}</dd></div>
            <div><dt class="text-muted">Branch</dt><dd>{{ $locumSession->branch->name }}</dd></div>
            <div><dt class="text-muted">Date</dt><dd>{{ $locumSession->session_date->format('d M Y') }}</dd></div>
            <div><dt class="text-muted">Time</dt><dd>{{ $locumSession->start_time }} - {{ $locumSession->end_time }}</dd></div>
            <div><dt class="text-muted">Status</dt><dd><span class="badge badge-secondary">{{ ucfirst($locumSession->status) }}</span></dd></div>
            <div><dt class="text-muted">Total Pay</dt><dd class="text-lg font-bold">RM {{ number_format($locumSession->total_pay, 2) }}</dd></div>
            <div><dt class="text-muted">Payment Status</dt>
                <dd>
                    @if($locumSession->is_paid)
                        <span class="badge badge-success">Paid</span>
                    @else
                        <span class="badge badge-danger">Unpaid</span>
                        <form method="POST" action="{{ route('locum-sessions.mark-paid', $locumSession) }}" class="d-inline ml-2">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-xs">Mark as Paid</button>
                        </form>
                    @endif
                </dd>
            </div>
            <div><dt class="text-muted">Notes</dt><dd>{{ $locumSession->notes ?? '-' }}</dd></div>
        </dl>

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('locum-sessions.edit', $locumSession) }}" class="btn btn-primary btn-sm">Edit</a>
            <a href="{{ route('locum-sessions.index') }}" class="btn btn-light btn-sm">Back</a>
        </div>
    </div>
</x-app-layout>
