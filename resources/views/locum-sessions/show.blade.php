<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-calendar-clock text-primary mr-2"></i>Locum Session</h4>
                <small class="text-muted">{{ $locumSession->locumDoctor->name }} · {{ $locumSession->session_date->format('d F Y') }}</small>
            </div>
            <div class="d-flex" style="gap:8px">
                <a href="{{ route('locum-sessions.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left mr-1"></i>Back</a>
                <a href="{{ route('locum-sessions.edit', $locumSession) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil mr-1"></i>Edit</a>
                @if(!$locumSession->is_paid)
                    <a href="{{ route('locum-payments.create', ['locum_doctor_id' => $locumSession->locum_doctor_id]) }}" class="btn btn-success btn-sm"><i class="mdi mdi-cash mr-1"></i>Pay Now</a>
                @endif
            </div>
        </div>
    </x-slot>

    @php
        $start = strtotime($locumSession->start_time);
        $end = strtotime($locumSession->end_time);
        $hours = ($end - $start) / 3600;
    @endphp

    {{-- Stat tiles --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid {{ $locumSession->is_paid ? 'var(--c-success)' : 'var(--c-warning)' }}">
                <span class="stat-pill-icon" style="background:{{ $locumSession->is_paid ? 'rgba(16,185,129,0.12);color:#047857' : 'rgba(245,158,11,0.12);color:#b45309' }}"><i class="mdi mdi-cash"></i></span>
                <div class="stat-pill-label">Total Pay</div>
                <div class="stat-pill-num">RM {{ number_format($locumSession->total_pay, 2) }}</div>
                <small class="{{ $locumSession->is_paid ? 'text-success' : 'text-warning' }}">{{ $locumSession->is_paid ? '✓ Paid' : 'Pending' }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-primary)">
                <span class="stat-pill-icon" style="background:rgba(14,165,233,0.12);color:#0369a1"><i class="mdi mdi-clock"></i></span>
                <div class="stat-pill-label">Duration</div>
                <div class="stat-pill-num">{{ number_format($hours, 1) }} hrs</div>
                <small class="text-muted">{{ $locumSession->start_time }} - {{ $locumSession->end_time }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-info)">
                <span class="stat-pill-icon" style="background:rgba(6,182,212,0.12);color:#0e7490"><i class="mdi mdi-stethoscope"></i></span>
                <div class="stat-pill-label">Consultations</div>
                <div class="stat-pill-num">{{ $consultationsCount }}</div>
                <small class="text-muted">{{ $consultationsCount === 0 ? 'none yet' : 'patients seen' }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            @php
                $statusColors = [
                    'scheduled' => ['#f59e0b','rgba(245,158,11,0.12)','#b45309'],
                    'in_progress' => ['#0ea5e9','rgba(14,165,233,0.12)','#0369a1'],
                    'completed' => ['#10b981','rgba(16,185,129,0.12)','#047857'],
                    'cancelled' => ['#ef4444','rgba(239,68,68,0.12)','#b91c1c'],
                ];
                $sc = $statusColors[$locumSession->status] ?? ['#64748b','rgba(100,116,139,0.12)','#475569'];
            @endphp
            <div class="stat-pill" style="border-left:4px solid {{ $sc[0] }}">
                <span class="stat-pill-icon" style="background:{{ $sc[1] }};color:{{ $sc[2] }}"><i class="mdi mdi-flag"></i></span>
                <div class="stat-pill-label">Status</div>
                <div class="stat-pill-num" style="font-size:1.2rem;text-transform:capitalize">{{ str_replace('_', ' ', $locumSession->status) }}</div>
                <small class="text-muted">{{ $locumSession->session_date->diffForHumans() }}</small>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Session details (left) --}}
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="mdi mdi-information-outline text-primary mr-2"></i>Session Details</h5>

                    <div class="d-flex align-items-center mb-3 pb-3" style="border-bottom:1px solid #e2e8f0">
                        <div class="locum-avatar mr-3">{{ strtoupper(substr($locumSession->locumDoctor->name, 0, 1)) }}</div>
                        <div>
                            <div class="font-weight-bold">{{ $locumSession->locumDoctor->name }}</div>
                            <small class="text-muted">{{ $locumSession->locumDoctor->specialization ?? 'General Practice' }}</small>
                            @if($locumSession->locumDoctor->mmc_number)<small class="d-block text-muted">MMC: {{ $locumSession->locumDoctor->mmc_number }}</small>@endif
                        </div>
                    </div>

                    <dl class="detail-list">
                        <div><dt>Branch</dt><dd><i class="mdi mdi-office-building text-info"></i> {{ $locumSession->branch->name }}</dd></div>
                        <div><dt>Date</dt><dd>{{ $locumSession->session_date->format('l, d F Y') }}</dd></div>
                        <div><dt>Time</dt><dd>{{ $locumSession->start_time }} → {{ $locumSession->end_time }} <small class="text-muted">({{ number_format($hours, 1) }} hours)</small></dd></div>
                        <div><dt>Hourly Rate</dt><dd>RM {{ number_format($locumSession->locumDoctor->hourly_rate ?? 0, 2) }}</dd></div>
                        <div><dt>Session Rate</dt><dd>RM {{ number_format($locumSession->locumDoctor->session_rate ?? 0, 2) }}</dd></div>
                        <div><dt>Calculated Pay</dt><dd class="text-success font-weight-bold">RM {{ number_format($locumSession->total_pay, 2) }}</dd></div>
                        @if($locumSession->notes)<div><dt>Notes</dt><dd>{{ $locumSession->notes }}</dd></div>@endif
                    </dl>

                    @if(!$locumSession->is_paid)
                        <div class="mt-3 d-flex" style="gap:8px">
                            <form method="POST" action="{{ route('locum-sessions.mark-paid', $locumSession) }}" class="d-inline" onsubmit="return confirm('Mark this single session as paid? For batch payment, use the Pay Now button instead.')">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-success btn-sm"><i class="mdi mdi-check mr-1"></i>Quick Mark Paid</button>
                            </form>
                            <form method="POST" action="{{ route('locum-sessions.destroy', $locumSession) }}" class="d-inline" onsubmit="return confirm('Delete this session?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete mr-1"></i>Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invitation card (right, if linked) --}}
        <div class="col-lg-5 mb-3">
            @if($invitation)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><i class="mdi mdi-email-fast text-info mr-2"></i>Source Invitation</h5>
                        <dl class="detail-list mb-3">
                            <div><dt>Invitation #</dt><dd>#{{ $invitation->id }}</dd></div>
                            <div><dt>Period</dt><dd><small>{{ $invitation->valid_from->format('d M, h:i A') }}<br>→ {{ $invitation->valid_to->format('d M, h:i A') }}</small></dd></div>
                            <div><dt>Permissions</dt><dd>
                                @if($invitation->can_consultation)<span class="badge badge-info"><i class="mdi mdi-stethoscope"></i> Consultations</span>@endif
                                @if($invitation->can_treatment_plan)<span class="badge badge-info"><i class="mdi mdi-clipboard-list"></i> Treatment Plans</span>@endif
                            </dd></div>
                            <div><dt>Status</dt><dd>
                                @php $colors = ['pending'=>'warning','accepted'=>'success','declined'=>'danger','revoked'=>'dark','expired'=>'secondary']; @endphp
                                <span class="badge badge-{{ $colors[$invitation->status] }}">{{ ucfirst($invitation->status) }}</span>
                            </dd></div>
                            @if($invitation->createdBy)<div><dt>Created By</dt><dd>{{ $invitation->createdBy->name }}</dd></div>@endif
                        </dl>
                        <a href="{{ route('locum-invitations.show', $invitation) }}" class="btn btn-outline-info btn-sm btn-block"><i class="mdi mdi-eye mr-1"></i>View Full Invitation</a>
                    </div>
                </div>
            @endif

            {{-- Consultations done --}}
            @if($consultationsCount > 0)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="mdi mdi-history text-success mr-2"></i>Consultations Done This Session</h5>
                        @foreach($consultations as $c)
                            <div class="py-2" style="border-bottom:1px solid #f1f5f9">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $c->consultation_number }}</strong>
                                        <div class="small text-muted">{{ $c->patient->name }}</div>
                                    </div>
                                    @php $sc = ['in_progress'=>'warning','completed'=>'success']; @endphp
                                    <span class="badge badge-{{ $sc[$c->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .locum-avatar {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
            color: #fff; font-weight: 800; font-size: 1.4rem;
            display: inline-flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(139,92,246,0.3);
        }
    </style>
</x-app-layout>
