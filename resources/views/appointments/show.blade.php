<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-calendar-check text-primary mr-1"></i>Appointment #{{ $appointment->id }}</h4>
                <small class="text-muted">{{ $appointment->appointment_date->format('l, d M Y') }} &middot; {{ substr($appointment->start_time, 0, 5) }}</small>
            </div>
            <div class="d-flex" style="gap:6px">
                <a href="{{ route('appointments.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> All Appointments</a>
                @if(!in_array($appointment->status, ['completed','cancelled']))
                    <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil"></i> Edit</a>
                @endif
            </div>
        </div>
    </x-slot>

    @php
        $statuses = ['pending','confirmed','in_progress','completed'];
        $statusColors = [
            'pending' => ['#f59e0b', 'warning', 'mdi-clock-outline'],
            'confirmed' => ['#3b82f6', 'info', 'mdi-calendar-check'],
            'in_progress' => ['#8b5cf6', 'primary', 'mdi-stethoscope'],
            'completed' => ['#10b981', 'success', 'mdi-check-circle'],
            'cancelled' => ['#ef4444', 'danger', 'mdi-close-circle'],
            'no_show' => ['#6b7280', 'secondary', 'mdi-account-off'],
        ];
        $currentStatus = $appointment->status;
        $currentIdx = array_search($currentStatus, $statuses);
        [$statusColor, $statusBadge, $statusIcon] = $statusColors[$currentStatus] ?? $statusColors['pending'];
        $isTerminal = in_array($currentStatus, ['cancelled','no_show','completed']);

        $patientAge = $appointment->patient->date_of_birth
            ? \Carbon\Carbon::parse($appointment->patient->date_of_birth)->age : null;
    @endphp

    {{-- Hero card --}}
    <div class="data-card mb-3" style="background:linear-gradient(135deg,{{ $statusColor }},{{ $statusColor }}dd);color:#fff;border:none;box-shadow:0 8px 24px rgba(0,0,0,0.15);position:relative;overflow:hidden">
        <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
        <div style="position:absolute;bottom:-50px;right:80px;width:140px;height:140px;background:rgba(255,255,255,0.04);border-radius:50%"></div>
        <div class="d-flex align-items-center flex-wrap" style="gap:18px;position:relative">
            <div style="background:#fff;border-radius:12px;padding:12px 18px;text-align:center;min-width:90px;box-shadow:0 2px 6px rgba(0,0,0,0.1)">
                <div style="color:{{ $statusColor }};font-size:11px;letter-spacing:0.1em;font-weight:700">{{ strtoupper($appointment->appointment_date->format('M')) }}</div>
                <div style="color:#1f2937;font-size:30px;font-weight:700;line-height:1">{{ $appointment->appointment_date->format('d') }}</div>
                <div style="color:#6b7280;font-size:11px;font-weight:600">{{ $appointment->appointment_date->format('Y') }}</div>
            </div>
            <div style="flex:1;min-width:200px">
                <small style="opacity:0.9;letter-spacing:0.05em;text-transform:uppercase;font-weight:700"><i class="mdi {{ $statusIcon }}"></i> {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}</small>
                <h3 class="text-white font-weight-bold mb-1 mt-1">{{ $appointment->appointment_date->format('l') }}, {{ $appointment->appointment_date->format('d M Y') }}</h3>
                <div class="d-flex flex-wrap" style="gap:14px;font-size:14px;opacity:0.95">
                    <span><i class="mdi mdi-clock-outline"></i> {{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}</span>
                    @if($appointment->branch)<span><i class="mdi mdi-hospital-building"></i> {{ $appointment->branch->name }}</span>@endif
                    <span><i class="mdi mdi-calendar"></i> {{ $appointment->appointment_date->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Status timeline --}}
    @if(!$isTerminal || $currentStatus === 'completed')
        <div class="data-card mb-3">
            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Status Progress</small>
            <div class="d-flex align-items-center mt-3" style="gap:0">
                @foreach($statuses as $i => $st)
                    @php
                        $stColor = $statusColors[$st][0];
                        $reached = $currentIdx !== false && $i <= $currentIdx;
                        $isCurrent = $st === $currentStatus;
                    @endphp
                    <div style="flex:1;text-align:center;position:relative">
                        <div style="width:40px;height:40px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;background:{{ $reached ? $stColor : '#e5e7eb' }};color:{{ $reached ? '#fff' : '#9ca3af' }};font-weight:700;border:3px solid {{ $isCurrent ? $stColor : 'transparent' }};box-shadow:{{ $isCurrent ? '0 0 0 4px '.$stColor.'33' : 'none' }};transition:all 0.2s">
                            <i class="mdi {{ $statusColors[$st][2] }}"></i>
                        </div>
                        <div class="mt-2" style="font-size:11px;font-weight:{{ $isCurrent ? '700' : '500' }};color:{{ $reached ? '#1f2937' : '#9ca3af' }};text-transform:uppercase;letter-spacing:0.03em">{{ str_replace('_', ' ', $st) }}</div>
                    </div>
                    @if($i < count($statuses) - 1)
                        <div style="height:3px;flex:1;margin:0 -8px 28px;background:{{ ($currentIdx !== false && $i < $currentIdx) ? $statusColors[$st][0] : '#e5e7eb' }};transition:background 0.2s"></div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Quick status update --}}
    @if(!$isTerminal)
        <div class="data-card mb-3" style="background:#f0f9ff;border:1px solid #bae6fd">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
                <div>
                    <small style="color:#075985;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                        <i class="mdi mdi-flash"></i> Quick Status Change
                    </small>
                </div>
                <div class="d-flex flex-wrap" style="gap:6px">
                    @foreach(['pending'=>'Pending','confirmed'=>'Confirm','in_progress'=>'Start','completed'=>'Complete','cancelled'=>'Cancel','no_show'=>'No Show'] as $st => $label)
                        @if($st !== $currentStatus)
                            <form method="POST" action="{{ route('appointments.update-status', $appointment) }}" class="d-inline" @if(in_array($st, ['cancelled','no_show'])) onsubmit="return confirm('Mark as {{ $label }}?')" @endif>
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $st }}">
                                <button type="submit" class="btn btn-sm btn-outline-{{ $statusColors[$st][1] }}">
                                    <i class="mdi {{ $statusColors[$st][2] }}"></i> {{ $label }}
                                </button>
                            </form>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        {{-- Patient + Doctor --}}
        <div class="col-lg-7">
            {{-- Patient --}}
            <div class="data-card mb-3">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap" style="gap:10px">
                    <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-account text-primary mr-1"></i>Patient</h5>
                    <a href="{{ route('patients.show', $appointment->patient) }}" class="btn btn-sm btn-outline-primary">View Profile <i class="mdi mdi-arrow-right"></i></a>
                </div>
                @php
                    $genderGrad = $appointment->patient->gender === 'male' ? 'linear-gradient(135deg,#1e40af,#1d4ed8)' :
                                 ($appointment->patient->gender === 'female' ? 'linear-gradient(135deg,#be185d,#9d174d)' : 'linear-gradient(135deg,#475569,#334155)');
                @endphp
                <div class="d-flex align-items-center flex-wrap" style="gap:14px">
                    <div style="width:64px;height:64px;border-radius:50%;background:{{ $genderGrad }};color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700">
                        {{ strtoupper(substr($appointment->patient->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:160px">
                        <div class="font-weight-bold" style="font-size:18px">{{ $appointment->patient->name }}</div>
                        <div class="text-muted small">{{ $appointment->patient->patient_id }}</div>
                        <div class="mt-1 d-flex flex-wrap" style="gap:8px;font-size:13px">
                            @if($patientAge !== null)<span><i class="mdi mdi-cake-variant text-muted"></i> {{ $patientAge }} yrs</span>@endif
                            @if($appointment->patient->gender)<span><i class="mdi mdi-{{ $appointment->patient->gender === 'male' ? 'gender-male' : 'gender-female' }} text-muted"></i> {{ ucfirst($appointment->patient->gender) }}</span>@endif
                            @if($appointment->patient->blood_type)<span><i class="mdi mdi-water text-danger"></i> {{ $appointment->patient->blood_type }}</span>@endif
                        </div>
                    </div>
                    @if($appointment->patient->phone)
                        <a href="tel:{{ $appointment->patient->phone }}" class="btn btn-sm btn-outline-success"><i class="mdi mdi-phone"></i> {{ $appointment->patient->phone }}</a>
                    @endif
                </div>
                @if($appointment->patient->allergies)
                    <div class="mt-3 p-2" style="background:#fee2e2;color:#991b1b;border-radius:6px;border-left:4px solid #dc2626">
                        <small class="font-weight-bold"><i class="mdi mdi-alert-octagon"></i> ALLERGIES</small>
                        <div class="small">{{ $appointment->patient->allergies }}</div>
                    </div>
                @endif
            </div>

            {{-- Doctor --}}
            <div class="data-card mb-3">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap" style="gap:10px">
                    <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-stethoscope text-success mr-1"></i>Doctor</h5>
                    <a href="{{ route('doctors.show', $appointment->doctor) }}" class="btn btn-sm btn-outline-success">View Profile <i class="mdi mdi-arrow-right"></i></a>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap:14px">
                    <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700">
                        {{ strtoupper(substr($appointment->doctor->user->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:160px">
                        <div class="font-weight-bold" style="font-size:18px">Dr. {{ $appointment->doctor->user->name }}</div>
                        <div class="text-muted small">{{ $appointment->doctor->specialization ?? 'General Practice' }}</div>
                        <div class="mt-1 d-flex flex-wrap" style="gap:8px;font-size:13px">
                            @if($appointment->doctor->mmc_number)<span><i class="mdi mdi-shield-check text-muted"></i> MMC {{ $appointment->doctor->mmc_number }}</span>@endif
                            @if($appointment->doctor->consultation_fee)<span><i class="mdi mdi-cash text-success"></i> RM {{ number_format($appointment->doctor->consultation_fee, 2) }}</span>@endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reason & Notes --}}
            <div class="data-card mb-3">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-note-text text-warning mr-1"></i>Reason &amp; Notes</h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Reason for Visit</small>
                        <div class="p-3 mt-1" style="background:#fffbeb;border-radius:8px;border:1px solid #fde68a;min-height:80px;color:#78350f">
                            {{ $appointment->reason ?? 'No reason specified' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Internal Notes</small>
                        <div class="p-3 mt-1" style="background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;min-height:80px">
                            {{ $appointment->notes ?? 'No notes' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column: queue, consultation, invoice --}}
        <div class="col-lg-5">
            {{-- Queue --}}
            <div class="data-card mb-3">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-ticket-account text-info mr-1"></i>Queue</h5>
                @if($appointment->queueEntry && !in_array($appointment->queueEntry->status, ['cancelled']))
                    @php
                        $qColors = ['waiting'=>['#f59e0b','warning'],'serving'=>['#3b82f6','info'],'completed'=>['#10b981','success'],'skipped'=>['#6b7280','secondary']];
                        [$qColor, $qBadge] = $qColors[$appointment->queueEntry->status] ?? ['#6b7280','secondary'];
                    @endphp
                    <div class="text-center p-3" style="background:linear-gradient(135deg,{{ $qColor }},{{ $qColor }}dd);color:#fff;border-radius:10px">
                        <small style="opacity:0.85;letter-spacing:0.1em;text-transform:uppercase;font-weight:700">No. Giliran</small>
                        <div style="font-size:48px;font-weight:700;line-height:1;margin:6px 0">{{ $appointment->queueEntry->queue_number }}</div>
                        <span class="badge badge-light text-dark">{{ ucfirst($appointment->queueEntry->status) }}</span>
                    </div>
                @elseif(in_array($appointment->status, ['pending', 'confirmed']) && $appointment->appointment_date->isToday())
                    <div class="text-center p-4" style="background:#f0fdf4;border-radius:10px;border:2px dashed #86efac">
                        <i class="mdi mdi-ticket-confirmation text-success" style="font-size:42px"></i>
                        <p class="mt-2 mb-2 small">Patient is here? Get queue number now.</p>
                        <form method="POST" action="{{ route('walk-in-queue.check-in', $appointment) }}">
                            @csrf
                            <button type="submit" class="btn btn-success font-weight-bold"><i class="mdi mdi-account-check"></i> Check In</button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="mdi mdi-ticket-outline" style="font-size:36px;opacity:0.4"></i>
                        <p class="small mt-2 mb-0">Not yet in queue</p>
                    </div>
                @endif
            </div>

            {{-- Consultation --}}
            <div class="data-card mb-3">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-stethoscope text-primary mr-1"></i>Consultation</h5>
                @if($appointment->consultation)
                    @php
                        $cColors = ['in_progress'=>['#f59e0b','warning'],'completed'=>['#10b981','success']];
                        [$cColor, $cBadge] = $cColors[$appointment->consultation->status] ?? ['#6b7280','secondary'];
                    @endphp
                    <div class="p-3" style="background:#eff6ff;border-radius:10px;border:1px solid #bfdbfe">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">CONSULTATION</small>
                                <div class="font-weight-bold">{{ $appointment->consultation->consultation_number }}</div>
                            </div>
                            <span class="badge badge-{{ $cBadge }}">{{ ucfirst(str_replace('_',' ',$appointment->consultation->status)) }}</span>
                        </div>
                        <div class="mt-2 d-flex flex-wrap" style="gap:6px">
                            <a href="{{ route('consultations.show', $appointment->consultation) }}" class="btn btn-sm btn-outline-info"><i class="mdi mdi-eye"></i> View</a>
                            @if($appointment->consultation->status === 'in_progress')
                                <a href="{{ route('consultations.edit', $appointment->consultation) }}" class="btn btn-sm btn-warning"><i class="mdi mdi-pencil"></i> Continue</a>
                            @endif
                        </div>
                    </div>
                @elseif(in_array($appointment->status, ['confirmed', 'in_progress']))
                    <div class="text-center p-4" style="background:#eff6ff;border-radius:10px;border:2px dashed #93c5fd">
                        <i class="mdi mdi-stethoscope text-primary" style="font-size:42px"></i>
                        <p class="mt-2 mb-2 small">Ready to begin consultation?</p>
                        <form method="POST" action="{{ route('consultations.start') }}">
                            @csrf
                            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                            <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-play-circle"></i> Start Consultation</button>
                        </form>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="mdi mdi-stethoscope" style="font-size:36px;opacity:0.4"></i>
                        <p class="small mt-2 mb-0">No consultation yet</p>
                    </div>
                @endif
            </div>

            {{-- Invoice --}}
            <div class="data-card mb-3">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-receipt text-success mr-1"></i>Invoice</h5>
                @if($appointment->invoice)
                    @php
                        $iColors = ['paid'=>['#10b981','success'],'pending'=>['#f59e0b','warning'],'partial'=>['#3b82f6','info'],'cancelled'=>['#6b7280','secondary']];
                        [$iColor, $iBadge] = $iColors[$appointment->invoice->status] ?? ['#6b7280','secondary'];
                    @endphp
                    <div class="p-3" style="background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">INVOICE</small>
                                <div class="font-weight-bold">{{ $appointment->invoice->invoice_number }}</div>
                                <div class="font-weight-bold text-success" style="font-size:18px">RM {{ number_format($appointment->invoice->total, 2) }}</div>
                            </div>
                            <span class="badge badge-{{ $iBadge }}">{{ ucfirst($appointment->invoice->status) }}</span>
                        </div>
                        <a href="{{ route('invoices.show', $appointment->invoice) }}" class="btn btn-sm btn-outline-success mt-2 btn-block"><i class="mdi mdi-eye"></i> View Invoice</a>
                    </div>
                @elseif($appointment->status === 'completed')
                    <div class="text-center p-4" style="background:#f0fdf4;border-radius:10px;border:2px dashed #86efac">
                        <i class="mdi mdi-receipt text-success" style="font-size:42px"></i>
                        <p class="mt-2 mb-2 small">Consultation done. Issue invoice?</p>
                        <a href="{{ route('invoices.create', ['appointment_id' => $appointment->id]) }}" class="btn btn-success font-weight-bold"><i class="mdi mdi-plus-circle"></i> Create Invoice</a>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="mdi mdi-receipt" style="font-size:36px;opacity:0.4"></i>
                        <p class="small mt-2 mb-0">Invoice will appear once completed</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Danger zone --}}
    @if(!in_array($currentStatus, ['completed','cancelled']))
        <div class="data-card mb-3" style="border-left:4px solid #ef4444">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
                <div>
                    <small class="text-danger font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                        <i class="mdi mdi-alert"></i> Danger Zone
                    </small>
                    <div class="small text-muted">Permanently delete this appointment</div>
                </div>
                <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" onsubmit="return confirm('Delete appointment #{{ $appointment->id }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete"></i> Delete Appointment</button>
                </form>
            </div>
        </div>
    @endif

    <style>
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
