@extends('locum-portal._layout')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h3 class="font-weight-bold mb-1"><i class="mdi mdi-clipboard-list text-info mr-2"></i>Treatment Plans</h3>
            <small class="text-muted">
                @if($invitation->treatment_plan_requires_approval)
                    <span class="badge badge-warning"><i class="mdi mdi-shield"></i> Plans require admin approval before activating</span>
                @else
                    <span class="badge badge-success"><i class="mdi mdi-check"></i> Plans auto-approved</span>
                @endif
            </small>
        </div>
        <a href="{{ route('locum-portal.treatment-plans.create') }}" class="btn btn-primary"><i class="mdi mdi-plus mr-1"></i>New Plan</a>
    </div>

    <div class="data-card">
        <table class="table">
            <thead><tr><th>Plan #</th><th>Patient</th><th>Title</th><th>Sessions</th><th>Approval</th><th>Created</th></tr></thead>
            <tbody>
                @forelse($plans as $p)
                    <tr>
                        <td><strong>{{ $p->plan_number }}</strong></td>
                        <td>{{ $p->patient->name }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($p->title, 40) }}</td>
                        <td>{{ $p->completed_sessions }} / {{ $p->total_sessions }}</td>
                        <td>
                            @php
                                $colors = [
                                    'auto_approved' => 'success', 'pending_approval' => 'warning',
                                    'approved' => 'success', 'rejected' => 'danger',
                                ];
                                $labels = [
                                    'auto_approved' => 'Auto-Approved', 'pending_approval' => 'Pending Approval',
                                    'approved' => 'Approved', 'rejected' => 'Rejected',
                                ];
                            @endphp
                            <span class="badge badge-{{ $colors[$p->approval_status] ?? 'secondary' }}">
                                {{ $labels[$p->approval_status] ?? $p->approval_status }}
                            </span>
                            @if($p->approval_status === 'rejected' && $p->rejection_reason)
                                <small class="d-block text-danger mt-1"><i class="mdi mdi-alert"></i> {{ $p->rejection_reason }}</small>
                            @endif
                        </td>
                        <td><small>{{ $p->created_at->diffForHumans() }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">
                        <i class="mdi mdi-clipboard-text-off" style="font-size:48px;opacity:0.3"></i>
                        <p>No treatment plans yet.</p>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $plans->links() }}
    </div>
@endsection
