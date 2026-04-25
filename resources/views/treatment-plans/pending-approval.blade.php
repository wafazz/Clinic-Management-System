<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-shield-check text-warning mr-2"></i>Treatment Plans — Pending Approval</h4>
                <small class="text-muted">Plans submitted by locums that need your review.</small>
            </div>
            <a href="{{ route('treatment-plans.index') }}" class="btn btn-light btn-sm">All Plans</a>
        </div>
    </x-slot>

    @forelse($plans as $plan)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:12px">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2" style="gap:10px">
                            <strong style="font-size:1.05rem">{{ $plan->plan_number }}</strong>
                            <span class="badge badge-warning"><i class="mdi mdi-clock"></i> Pending</span>
                            <span class="badge badge-info">{{ $plan->total_sessions }} sessions</span>
                        </div>
                        <h5 class="mb-2 font-weight-bold">{{ $plan->title }}</h5>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase d-block" style="font-size:0.7rem">Patient</small>
                                <strong>{{ $plan->patient->name }}</strong>
                                <small class="text-muted d-block">{{ $plan->patient->patient_id }}</small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase d-block" style="font-size:0.7rem">Submitted by</small>
                                <strong>{{ $plan->createdByLocum->name ?? '—' }}</strong>
                                <small class="text-muted d-block">{{ $plan->createdByLocum->specialization ?? '—' }} (Locum)</small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase d-block" style="font-size:0.7rem">Period</small>
                                <strong>{{ $plan->start_date->format('d M Y') }}</strong>
                                <small class="text-muted d-block">→ {{ $plan->expected_end_date?->format('d M Y') }}</small>
                            </div>
                        </div>
                        @if($plan->diagnosis)<p class="mb-1"><strong>Diagnosis:</strong> {{ $plan->diagnosis }}</p>@endif
                        @if($plan->description)<p class="mb-1 small text-muted">{{ $plan->description }}</p>@endif
                        @if($plan->notes)<p class="small mt-2" style="background:#fef3c7;border-left:3px solid #f59e0b;padding:8px 12px;border-radius:6px"><strong>Locum's note:</strong> {{ $plan->notes }}</p>@endif
                        <small class="text-muted">Submitted {{ $plan->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="d-flex flex-column" style="gap:8px;min-width:160px">
                        <form method="POST" action="{{ route('treatment-plans.approve', $plan) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-block"><i class="mdi mdi-check-bold mr-1"></i>Approve</button>
                        </form>
                        <button class="btn btn-outline-danger btn-block" onclick="document.getElementById('reject-{{ $plan->id }}').classList.toggle('d-none')">
                            <i class="mdi mdi-close mr-1"></i>Reject
                        </button>
                        <a href="{{ route('treatment-plans.show', $plan) }}" class="btn btn-link btn-sm">View details →</a>
                    </div>
                </div>

                <form method="POST" action="{{ route('treatment-plans.reject', $plan) }}" class="d-none mt-3" id="reject-{{ $plan->id }}">
                    @csrf @method('PATCH')
                    <div class="alert alert-danger py-2 mb-2 small"><i class="mdi mdi-alert-circle mr-1"></i>Rejecting this plan will also cancel it. The locum will see your reason.</div>
                    <textarea name="rejection_reason" required class="form-control mb-2" rows="2" placeholder="Reason for rejection (visible to locum)..."></textarea>
                    <button class="btn btn-danger btn-sm">Confirm Reject</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-center py-5">
            <i class="mdi mdi-check-circle" style="font-size:64px;color:#10b981;opacity:0.5"></i>
            <h5 class="mt-3">All caught up!</h5>
            <p class="text-muted mb-0">No treatment plans waiting for approval.</p>
        </div></div>
    @endforelse

    <div>{{ $plans->links() }}</div>
</x-app-layout>
