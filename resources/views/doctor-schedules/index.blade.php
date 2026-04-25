<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-calendar-clock text-primary mr-1"></i>Weekly Schedule</h4>
                <small class="text-muted">Set when Dr. {{ $doctor->user->name }} is available</small>
            </div>
            <div class="d-flex" style="gap:6px">
                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Doctor</a>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Hero card --}}
    <div class="data-card mb-3" style="background:linear-gradient(135deg,#1e40af,#1e3a8a);color:#fff;border:none;box-shadow:0 8px 24px rgba(30,64,175,0.25)">
        <div class="d-flex align-items-center flex-wrap" style="gap:18px">
            <div style="width:72px;height:72px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:700;border:3px solid rgba(255,255,255,0.3)">
                {{ strtoupper(substr($doctor->user->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:200px">
                <h3 class="text-white font-weight-bold mb-1">Dr. {{ $doctor->user->name }}</h3>
                <div style="opacity:0.9">
                    <i class="mdi mdi-stethoscope"></i> {{ $doctor->specialization ?? 'General Practice' }}
                    @if($doctor->branch)
                        <span class="mx-2">·</span>
                        <i class="mdi mdi-hospital-building"></i> {{ $doctor->branch->name }}
                    @endif
                </div>
            </div>
            <div class="d-flex" style="gap:24px">
                <div class="text-center">
                    <div style="font-size:28px;font-weight:700;line-height:1">{{ $weeklyHours }}</div>
                    <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase">Hrs/Week</small>
                </div>
                <div class="text-center">
                    <div style="font-size:28px;font-weight:700;line-height:1">{{ $totalSlots }}</div>
                    <small style="opacity:0.85;letter-spacing:0.05em;text-transform:uppercase">Slots/Week</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat tiles --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-primary">{{ $daysConfigured }}</div><div class="label">Days Configured</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-success">{{ $schedules->where('is_available', true)->count() }}</div><div class="label">Available Days</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-warning">{{ $daysOff }}</div><div class="label">Days Off</div></div></div>
        <div class="col-md-3 col-6 mb-3"><div class="stat-card"><div class="num text-info">{{ $weeklyHours }}h</div><div class="label">Total Hours/Wk</div></div></div>
    </div>

    {{-- Visual weekly grid --}}
    <div class="data-card mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap" style="gap:10px">
            <div>
                <h5 class="mb-0 font-weight-bold"><i class="mdi mdi-view-week text-primary mr-1"></i>Week at a Glance</h5>
                <small class="text-muted">Click a day card to edit it</small>
            </div>
            <div class="small">
                <span style="display:inline-block;width:12px;height:12px;background:#10b981;border-radius:3px;vertical-align:middle"></span> Available
                <span style="display:inline-block;width:12px;height:12px;background:#ef4444;border-radius:3px;vertical-align:middle;margin-left:8px"></span> Unavailable
                <span style="display:inline-block;width:12px;height:12px;background:#e5e7eb;border-radius:3px;vertical-align:middle;margin-left:8px"></span> Not set
            </div>
        </div>

        <div class="row" id="weekGrid">
            @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $i => $dayName)
                @php $s = $scheduleByDay[$i] ?? null; @endphp
                <div class="col-md col-6 mb-3">
                    <div onclick="loadDay({{ $i }})"
                         style="cursor:pointer;border-radius:10px;padding:14px;min-height:130px;transition:transform 0.15s,box-shadow 0.15s;border:2px solid transparent;
                         @if($s && $s->is_available) background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 12px rgba(16,185,129,0.25)
                         @elseif($s) background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;box-shadow:0 4px 12px rgba(239,68,68,0.25)
                         @else background:#f3f4f6;color:#6b7280;border:2px dashed #d1d5db
                         @endif"
                         onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 20px rgba(0,0,0,0.15)'"
                         onmouseout="this.style.transform='translateY(0)';this.style.boxShadow=''">
                        <div style="font-weight:700;font-size:11px;letter-spacing:0.08em;text-transform:uppercase;opacity:0.85">
                            {{ substr($dayName, 0, 3) }}
                        </div>
                        <div style="font-weight:700;font-size:18px;margin-top:2px">{{ $dayName }}</div>
                        @if($s)
                            <div style="margin-top:8px;font-size:13px;font-weight:600">
                                <i class="mdi mdi-clock-outline"></i>
                                {{ substr($s->start_time, 0, 5) }} – {{ substr($s->end_time, 0, 5) }}
                            </div>
                            <div style="font-size:11px;opacity:0.9;margin-top:4px">
                                {{ $s->slot_duration }}min slots
                                @if($s->is_available)
                                    @php
                                        $mins = (strtotime($s->end_time) - strtotime($s->start_time)) / 60;
                                        $slots = $s->slot_duration > 0 ? floor($mins / $s->slot_duration) : 0;
                                    @endphp
                                    · {{ $slots }} slots
                                @endif
                            </div>
                            @if(!$s->is_available)
                                <span class="badge badge-light mt-2"><i class="mdi mdi-close-circle"></i> Unavailable</span>
                            @endif
                        @else
                            <div style="margin-top:18px;font-size:12px;opacity:0.7;text-align:center">
                                <i class="mdi mdi-plus-circle" style="font-size:22px;display:block;margin-bottom:4px"></i>
                                Tap to set
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row">
        {{-- Schedule list table --}}
        <div class="col-lg-7 mb-3">
            <div class="data-card">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-format-list-bulleted text-primary mr-1"></i>All Schedules</h5>
                @if($schedules->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Slot</th>
                                    <th>Slots/day</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    @php
                                        $mins = (strtotime($schedule->end_time) - strtotime($schedule->start_time)) / 60;
                                        $slots = $schedule->slot_duration > 0 ? floor($mins / $schedule->slot_duration) : 0;
                                    @endphp
                                    <tr>
                                        <td class="font-weight-bold">{{ $schedule->day_name }}</td>
                                        <td>{{ substr($schedule->start_time, 0, 5) }} – {{ substr($schedule->end_time, 0, 5) }}</td>
                                        <td>{{ $schedule->slot_duration }} min</td>
                                        <td><span class="badge badge-info">{{ $slots }}</span></td>
                                        <td>
                                            @if($schedule->is_available)
                                                <span class="badge badge-success"><i class="mdi mdi-check"></i> Available</span>
                                            @else
                                                <span class="badge badge-danger"><i class="mdi mdi-close"></i> Unavailable</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <button type="button" onclick="loadDay({{ $schedule->day_of_week }})" class="btn btn-outline-primary btn-sm py-1 px-2"><i class="mdi mdi-pencil"></i></button>
                                            <form method="POST" action="{{ route('doctor-schedules.destroy', $schedule) }}" class="d-inline" onsubmit="return confirm('Remove {{ $schedule->day_name }} schedule?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm py-1 px-2"><i class="mdi mdi-delete"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="mdi mdi-calendar-blank-outline text-muted" style="font-size:64px"></i>
                        <p class="text-muted mt-2 mb-0">No schedule set yet.</p>
                        <small class="text-muted">Use the form on the right to add one — or try a quick template below.</small>
                    </div>
                @endif
            </div>
        </div>

        {{-- Add/Edit form --}}
        <div class="col-lg-5 mb-3" x-data="schedForm()" x-init="init()">
            <div class="data-card" style="position:sticky;top:80px">
                <h5 class="mb-3 font-weight-bold"><i class="mdi mdi-pencil-plus text-success mr-1"></i><span x-text="dayOfWeek !== '' ? 'Edit ' + dayName(dayOfWeek) : 'Add Schedule'"></span></h5>

                <form method="POST" action="{{ route('doctor-schedules.store', $doctor) }}" id="schedFormEl">
                    @csrf

                    {{-- Day pills picker --}}
                    <label class="form-label small font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Day *</label>
                    <div class="d-flex flex-wrap mb-3" style="gap:6px">
                        @foreach(['S','M','T','W','T','F','S'] as $i => $letter)
                            <button type="button" @click="setDay({{ $i }})"
                                :style="dayOfWeek == {{ $i }} ? 'background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border-color:#2563eb' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                style="width:42px;height:42px;border-radius:50%;border:2px solid;font-weight:700;transition:all 0.15s">
                                {{ $letter }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="day_of_week" :value="dayOfWeek" required>

                    <div class="row">
                        <div class="col-6">
                            <label class="form-label small font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Start *</label>
                            <input type="time" name="start_time" class="form-control" x-model="startTime" required />
                        </div>
                        <div class="col-6">
                            <label class="form-label small font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">End *</label>
                            <input type="time" name="end_time" class="form-control" x-model="endTime" required />
                        </div>
                    </div>

                    {{-- Time presets --}}
                    <div class="mt-2 mb-3 d-flex flex-wrap" style="gap:6px">
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="setTime('09:00','17:00')">9–5</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="setTime('08:00','12:00')">Morning</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="setTime('14:00','18:00')">Afternoon</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="setTime('18:00','22:00')">Evening</button>
                    </div>

                    <label class="form-label small font-weight-bold mt-2" style="text-transform:uppercase;letter-spacing:0.05em">Slot Duration *</label>
                    <div class="d-flex flex-wrap mb-2" style="gap:6px">
                        @foreach([15, 20, 30, 45, 60] as $d)
                            <button type="button" @click="slotDuration = {{ $d }}"
                                :style="slotDuration == {{ $d }} ? 'background:#1e40af;color:#fff;border-color:#1e40af' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                style="padding:6px 14px;border-radius:6px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s">
                                {{ $d }} min
                            </button>
                        @endforeach
                    </div>
                    <input type="number" name="slot_duration" min="5" max="120" required class="form-control form-control-sm" x-model="slotDuration" />

                    {{-- Availability toggle --}}
                    <div class="mt-3 p-3" style="background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                        <input type="hidden" name="is_available" value="0" />
                        <label class="d-flex align-items-center mb-0" style="gap:10px;cursor:pointer">
                            <input type="checkbox" name="is_available" value="1" x-model="isAvailable" style="display:none">
                            <span :style="isAvailable ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="isAvailable ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold" x-text="isAvailable ? 'Available' : 'Unavailable'"></span>
                                <small class="d-block text-muted" x-text="isAvailable ? 'Patients can book this day' : 'No bookings allowed'"></small>
                            </span>
                        </label>
                    </div>

                    {{-- Live slot preview --}}
                    <div class="mt-3 p-3" style="background:linear-gradient(135deg,#fef3c7,#fde68a);border-radius:8px;border:1px solid #f59e0b" x-show="slotsCount > 0" x-cloak>
                        <small style="color:#92400e;font-weight:700;text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-flash"></i> Will Generate
                        </small>
                        <div class="font-weight-bold mt-1" style="color:#78350f">
                            <span x-text="slotsCount"></span> slots
                            <span class="text-muted small">· <span x-text="durationLabel"></span> total</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mt-3 font-weight-bold">
                        <i class="mdi mdi-content-save mr-1"></i> Save <span x-text="dayOfWeek !== '' ? dayName(dayOfWeek) : 'Schedule'"></span>
                    </button>
                </form>

                {{-- Quick templates --}}
                <hr class="my-3">
                <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                    <i class="mdi mdi-flash"></i> Quick Templates
                </small>
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-info btn-block mb-2" @click="applyTemplate('weekday-9to5')">
                        <i class="mdi mdi-briefcase"></i> Mon–Fri, 9:00–17:00 (30min)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info btn-block mb-2" @click="applyTemplate('weekday-half')">
                        <i class="mdi mdi-coffee"></i> Mon–Fri, 9:00–13:00 (30min)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info btn-block" @click="applyTemplate('weekend')">
                        <i class="mdi mdi-calendar-weekend"></i> Sat–Sun, 9:00–13:00 (30min)
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden bulk-apply form for templates --}}
    <form id="bulkForm" method="POST" action="{{ route('doctor-schedules.store', $doctor) }}" style="display:none">
        @csrf
        <input type="hidden" name="day_of_week" id="bulk_day">
        <input type="hidden" name="start_time" id="bulk_start">
        <input type="hidden" name="end_time" id="bulk_end">
        <input type="hidden" name="slot_duration" id="bulk_slot">
        <input type="hidden" name="is_available" value="1">
    </form>

    <script>
        const SCHEDULE_DATA = @json($scheduleByDay->map(fn($s) => [
            'day_of_week' => $s->day_of_week,
            'start_time' => substr($s->start_time, 0, 5),
            'end_time' => substr($s->end_time, 0, 5),
            'slot_duration' => $s->slot_duration,
            'is_available' => (bool) $s->is_available,
        ]));

        function loadDay(day) {
            window.dispatchEvent(new CustomEvent('load-day', { detail: day }));
            document.getElementById('schedFormEl').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function schedForm() {
            return {
                dayOfWeek: '',
                startTime: '09:00',
                endTime: '17:00',
                slotDuration: 30,
                isAvailable: true,
                init() {
                    window.addEventListener('load-day', (e) => this.setDay(e.detail));
                },
                setDay(d) {
                    this.dayOfWeek = d;
                    if (SCHEDULE_DATA[d]) {
                        const s = SCHEDULE_DATA[d];
                        this.startTime = s.start_time;
                        this.endTime = s.end_time;
                        this.slotDuration = s.slot_duration;
                        this.isAvailable = s.is_available;
                    }
                },
                setTime(start, end) {
                    this.startTime = start;
                    this.endTime = end;
                },
                dayName(d) {
                    return ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][d] || '';
                },
                get totalMinutes() {
                    if (!this.startTime || !this.endTime) return 0;
                    const [sh, sm] = this.startTime.split(':').map(Number);
                    const [eh, em] = this.endTime.split(':').map(Number);
                    return Math.max(0, (eh * 60 + em) - (sh * 60 + sm));
                },
                get slotsCount() {
                    if (!this.isAvailable || !this.slotDuration) return 0;
                    return Math.floor(this.totalMinutes / this.slotDuration);
                },
                get durationLabel() {
                    const m = this.totalMinutes;
                    if (m < 60) return m + ' min';
                    const h = Math.floor(m / 60), r = m % 60;
                    return r === 0 ? h + ' hr' : h + ' hr ' + r + ' min';
                },
                applyTemplate(name) {
                    if (!confirm('This will add/update multiple days. Continue?')) return;
                    const templates = {
                        'weekday-9to5': { days: [1,2,3,4,5], start: '09:00', end: '17:00', slot: 30 },
                        'weekday-half': { days: [1,2,3,4,5], start: '09:00', end: '13:00', slot: 30 },
                        'weekend':      { days: [0,6],       start: '09:00', end: '13:00', slot: 30 },
                    };
                    const t = templates[name];
                    if (!t) return;
                    submitTemplate(t, 0);
                },
            };
        }

        function submitTemplate(t, i) {
            if (i >= t.days.length) return;
            const form = document.getElementById('bulkForm');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            const fd = new FormData();
            fd.append('_token', document.querySelector('meta[name=csrf-token]')?.content || form.querySelector('[name=_token]').value);
            fd.append('day_of_week', t.days[i]);
            fd.append('start_time', t.start);
            fd.append('end_time', t.end);
            fd.append('slot_duration', t.slot);
            fd.append('is_available', '1');
            xhr.onload = () => {
                if (i === t.days.length - 1) window.location.reload();
                else submitTemplate(t, i + 1);
            };
            xhr.send(fd);
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
        .stat-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px; text-align:center; }
        .stat-card .num { font-size:26px; font-weight:700; line-height:1.1; }
        .stat-card .label { font-size:11px; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-top:4px; }
    </style>
</x-app-layout>
