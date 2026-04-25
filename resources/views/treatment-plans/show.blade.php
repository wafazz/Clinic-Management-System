<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Plan {{ $treatmentPlan->plan_number }}</h4></x-slot>
    <div class="row">
        <div class="col-md-5">
            <div class="card mb-3"><div class="card-body">
                <h5>{{ $treatmentPlan->title }}</h5>
                <p><strong>Patient:</strong> {{ $treatmentPlan->patient->name }}</p>
                <p><strong>Doctor:</strong> Dr. {{ $treatmentPlan->doctor->user->name }}</p>
                <p><strong>Diagnosis:</strong> {{ $treatmentPlan->diagnosis ?? '-' }}</p>
                <p><strong>Status:</strong> <span class="badge badge-{{ $treatmentPlan->status === 'completed' ? 'info' : 'success' }}">{{ ucfirst(str_replace('_', ' ', $treatmentPlan->status)) }}</span></p>
                <p><strong>Progress:</strong> {{ $treatmentPlan->completed_sessions }} / {{ $treatmentPlan->total_sessions }}</p>
                <p><strong>Start:</strong> {{ $treatmentPlan->start_date->format('d M Y') }}</p>
                <p><strong>Expected End:</strong> {{ $treatmentPlan->expected_end_date?->format('d M Y') }}</p>
                @if($treatmentPlan->description)<p><strong>Description:</strong> {{ $treatmentPlan->description }}</p>@endif
            </div></div>
        </div>
        <div class="col-md-7">
            <div class="card"><div class="card-body">
                <h5>Sessions</h5>
                <table class="table table-sm"><thead><tr><th>#</th><th>Date</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead><tbody>
                    @foreach($treatmentPlan->sessions as $s)
                        <tr>
                            <td>{{ $s->session_number }}</td>
                            <td>{{ $s->scheduled_date?->format('d M Y') }}</td>
                            <td>
                                @php $colors = ['pending'=>'badge-secondary','scheduled'=>'badge-info','completed'=>'badge-success','skipped'=>'badge-warning','cancelled'=>'badge-danger']; @endphp
                                <span class="badge {{ $colors[$s->status] }}">{{ ucfirst($s->status) }}</span>
                            </td>
                            <td><small>{{ $s->doctor_notes ?? '-' }}</small></td>
                            <td>
                                @if($s->status !== 'completed')
                                    <form method="POST" action="{{ route('treatment-plan-sessions.complete', $s) }}" class="d-inline">@csrf @method('PATCH')<button class="btn btn-outline-success btn-sm py-1 px-2" title="Mark Done"><i class="mdi mdi-check"></i></button></form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody></table>
            </div></div>
        </div>
    </div>
</x-app-layout>
