@extends('locum-portal._layout')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h3 class="font-weight-bold mb-1"><i class="mdi mdi-stethoscope text-success mr-2"></i>Consultations</h3>
            <small class="text-muted">{{ $invitation->branch->name }} · session ends {{ $invitation->valid_to->diffForHumans() }}</small>
        </div>
    </div>

    <div class="row">
        {{-- Queue --}}
        <div class="col-lg-7 mb-3">
            <div class="data-card">
                <h5 class="mb-3"><i class="mdi mdi-account-clock text-primary mr-1"></i>Queue ({{ $queueWaiting->count() }})</h5>
                @forelse($queueWaiting as $q)
                    <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                        <div class="d-flex align-items-center" style="gap:12px">
                            <div style="background:#eff6ff;color:#0369a1;font-weight:800;font-size:1.2rem;padding:8px 14px;border-radius:8px;min-width:60px;text-align:center">
                                {{ $q->queue_number }}
                            </div>
                            <div>
                                <div class="font-weight-bold">{{ $q->patient_name }}</div>
                                <small class="text-muted">
                                    {{ $q->patient->ic_number ?? '—' }}
                                    @if($q->is_priority)<span class="badge badge-danger ml-1"><i class="mdi mdi-star"></i> Priority</span>@endif
                                </small>
                                @if($q->reason)<div class="small text-muted"><i class="mdi mdi-information-outline"></i> {{ \Illuminate\Support\Str::limit($q->reason, 60) }}</div>@endif
                            </div>
                        </div>
                        <div>
                            @if($q->status === 'serving')
                                <span class="badge badge-info">Serving</span>
                                @if($q->consultation && $q->consultation->locum_doctor_id == $locum->id)
                                    <a href="{{ route('locum-portal.consultations.edit', $q->consultation) }}" class="btn btn-success btn-sm ml-2"><i class="mdi mdi-pencil"></i> Continue</a>
                                @endif
                            @else
                                <form method="POST" action="{{ route('locum-portal.consultations.start') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="walk_in_queue_id" value="{{ $q->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm" {{ !$q->patient_id ? 'disabled' : '' }}>
                                        <i class="mdi mdi-play"></i> Start
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="mdi mdi-account-multiple-outline" style="font-size:48px;opacity:0.3"></i>
                        <p>No patients in queue right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- My consultations --}}
        <div class="col-lg-5 mb-3">
            <div class="data-card">
                <h5 class="mb-3"><i class="mdi mdi-history text-secondary mr-1"></i>My Recent Consultations</h5>
                @forelse($myConsultations as $c)
                    <div class="py-2" style="border-bottom:1px solid #f1f5f9">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $c->consultation_number }}</strong>
                                <div class="small text-muted">{{ $c->patient->name }} · {{ $c->created_at->format('h:i A') }}</div>
                            </div>
                            @php $colors = ['in_progress'=>'warning','completed'=>'success']; @endphp
                            <span class="badge badge-{{ $colors[$c->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
                        </div>
                        @if($c->status === 'in_progress')
                            <a href="{{ route('locum-portal.consultations.edit', $c) }}" class="small text-primary">Continue →</a>
                        @endif
                    </div>
                @empty
                    <p class="text-muted text-center small mt-3">No consultations yet this session.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
