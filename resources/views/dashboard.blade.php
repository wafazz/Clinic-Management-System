<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold mb-0">
                    <i class="mdi mdi-view-dashboard text-primary mr-2"></i>Dashboard
                </h4>
                <small class="text-muted">{{ now()->format('l, d F Y') }} · Welcome back, {{ auth()->user()->name }}</small>
            </div>
            @if($currentBranch)
                <span class="badge badge-pill p-2 px-3" style="background:#eff6ff;color:#1e40af;font-size:0.85em;font-weight:600;"><i class="mdi mdi-office-building mr-1"></i>{{ $currentBranch->name }}</span>
            @endif
        </div>
    </x-slot>

    {{-- HERO BANNER --}}
    <div class="dashboard-hero">
        <div class="dashboard-hero-bg"></div>
        <div class="dashboard-hero-content">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h2 class="text-white font-weight-bold mb-2"><i class="mdi mdi-{{ now()->hour < 12 ? 'weather-sunny' : (now()->hour < 18 ? 'weather-partly-cloudy' : 'weather-night') }} mr-2"></i>{{ now()->hour < 12 ? 'Good Morning' : (now()->hour < 18 ? 'Good Afternoon' : 'Good Evening') }}, {{ explode(' ', auth()->user()->name)[0] }}</h2>
                    <p class="text-white opacity-90 mb-3">Here's what's happening at the clinic today.</p>
                    <div class="d-flex flex-wrap" style="gap:8px;">
                        <a href="{{ route('walk-in-queue.create') }}" class="btn btn-light btn-sm font-weight-bold"><i class="mdi mdi-plus mr-1"></i>New Walk-In</a>
                        <a href="{{ route('appointments.create') }}" class="btn btn-outline-light btn-sm font-weight-bold"><i class="mdi mdi-calendar-plus mr-1"></i>Book Appointment</a>
                        <a href="{{ route('patients.create') }}" class="btn btn-outline-light btn-sm font-weight-bold"><i class="mdi mdi-account-plus mr-1"></i>Register Patient</a>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="hero-stat-grid">
                        <div class="hero-stat">
                            <div class="hero-stat-num">{{ $queueWaiting }}</div>
                            <div class="hero-stat-label">Waiting</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">{{ $queueServing }}</div>
                            <div class="hero-stat-label">Serving</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">{{ $inProgressConsultations }}</div>
                            <div class="hero-stat-label">In Consult</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">{{ $queueCompleted }}</div>
                            <div class="hero-stat-label">Done Today</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CURRENT SERVING (if any) --}}
    @if($currentServing)
    <div class="alert alert-info d-flex align-items-center justify-content-between mb-3" style="border-left:6px solid #0ea5e9;">
        <div>
            <small class="text-muted d-block">Now Serving</small>
            <strong style="font-size:1.5em;color:#0ea5e9;">{{ $currentServing->queue_number }}</strong>
            <span class="ml-2">{{ $currentServing->patient_name }}</span>
            @if($currentServing->doctor)<small class="text-muted ml-2">— Dr. {{ $currentServing->doctor->user->name }}</small>@endif
            @if($currentServing->is_priority)<span class="badge badge-danger ml-2"><i class="mdi mdi-star"></i> Priority</span>@endif
        </div>
        <a href="{{ route('walk-in-queue.index') }}" class="btn btn-info btn-sm">View Queue <i class="mdi mdi-arrow-right ml-1"></i></a>
    </div>
    @endif

    {{-- KPI CARDS Row 1 - Big Gradient --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 grid-margin stretch-card">
            <div class="kpi-card kpi-blue">
                <div class="kpi-icon"><i class="mdi mdi-account-multiple"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Total Patients</div>
                    <div class="kpi-value">{{ number_format($totalPatients) }}</div>
                    <div class="kpi-trend">
                        @if($patientGrowth > 0)
                            <i class="mdi mdi-trending-up"></i> +{{ $patientGrowth }}% vs last month
                        @elseif($patientGrowth < 0)
                            <i class="mdi mdi-trending-down"></i> {{ $patientGrowth }}% vs last month
                        @else
                            <i class="mdi mdi-minus"></i> No change
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 grid-margin stretch-card">
            <div class="kpi-card kpi-cyan">
                <div class="kpi-icon"><i class="mdi mdi-calendar-check"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Today's Appointments</div>
                    <div class="kpi-value">{{ $todayAppointments }}</div>
                    <div class="kpi-trend"><i class="mdi mdi-check-circle"></i> {{ $completedToday }} completed · {{ $pendingAppointments }} pending</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 grid-margin stretch-card">
            <div class="kpi-card kpi-green">
                <div class="kpi-icon"><i class="mdi mdi-cash-multiple"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Monthly Revenue</div>
                    <div class="kpi-value">RM {{ number_format($monthlyRevenue, 0) }}</div>
                    <div class="kpi-trend">
                        @if($revenueGrowth > 0)
                            <i class="mdi mdi-trending-up"></i> +{{ $revenueGrowth }}% vs last month
                        @elseif($revenueGrowth < 0)
                            <i class="mdi mdi-trending-down"></i> {{ $revenueGrowth }}% vs last month
                        @else
                            <i class="mdi mdi-minus"></i> Same as last month
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 grid-margin stretch-card">
            <div class="kpi-card kpi-purple">
                <div class="kpi-icon"><i class="mdi mdi-card-account-details"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Active Members</div>
                    <div class="kpi-value">{{ number_format($activeMembers) }}</div>
                    <div class="kpi-trend"><i class="mdi mdi-star"></i> {{ $activeMembers > 0 ? round(($activeMembers / max($totalPatients, 1)) * 100, 1) : 0 }}% of patients</div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Row 2 - Smaller Tiles --}}
    <div class="row">
        <div class="col-lg-2 col-md-4 col-6 grid-margin stretch-card">
            <a href="{{ route('doctors.index') }}" class="text-decoration-none">
                <div class="mini-kpi mini-kpi-info">
                    <i class="mdi mdi-stethoscope"></i>
                    <div class="mini-kpi-num">{{ $totalDoctors }}</div>
                    <div class="mini-kpi-label">Active Doctors</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6 grid-margin stretch-card">
            <a href="{{ route('medicines.index') }}" class="text-decoration-none">
                <div class="mini-kpi {{ $lowStockMedicines > 0 ? 'mini-kpi-danger' : 'mini-kpi-success' }}">
                    <i class="mdi mdi-pill-off"></i>
                    <div class="mini-kpi-num">{{ $lowStockMedicines }}</div>
                    <div class="mini-kpi-label">Low Stock</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6 grid-margin stretch-card">
            <a href="{{ route('insurance-claims.index') }}" class="text-decoration-none">
                <div class="mini-kpi mini-kpi-warning">
                    <i class="mdi mdi-shield-check"></i>
                    <div class="mini-kpi-num">{{ $pendingClaims }}</div>
                    <div class="mini-kpi-label">Pending Claims</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6 grid-margin stretch-card">
            <a href="{{ route('leads.index') }}" class="text-decoration-none">
                <div class="mini-kpi mini-kpi-pink">
                    <i class="mdi mdi-account-search"></i>
                    <div class="mini-kpi-num">{{ $newLeads }}</div>
                    <div class="mini-kpi-label">New Leads</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6 grid-margin stretch-card">
            <a href="{{ route('leads.index') }}" class="text-decoration-none">
                <div class="mini-kpi mini-kpi-orange">
                    <i class="mdi mdi-bell-ring"></i>
                    <div class="mini-kpi-num">{{ $followUpsDueToday }}</div>
                    <div class="mini-kpi-label">Follow-ups Today</div>
                </div>
            </a>
        </div>
        <div class="col-lg-2 col-md-4 col-6 grid-margin stretch-card">
            <a href="{{ route('consultations.index') }}" class="text-decoration-none">
                <div class="mini-kpi mini-kpi-teal">
                    <i class="mdi mdi-stethoscope"></i>
                    <div class="mini-kpi-num">{{ $inProgressConsultations }}</div>
                    <div class="mini-kpi-label">In Consult</div>
                </div>
            </a>
        </div>
    </div>

    {{-- CHARTS Row --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0"><i class="mdi mdi-chart-line text-success mr-2"></i>Revenue Trend</h4>
                        <small class="text-muted">Last 6 months</small>
                    </div>
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0"><i class="mdi mdi-chart-donut text-info mr-2"></i>Appointments</h4>
                        <small class="text-muted">This month</small>
                    </div>
                    <canvas id="appointmentPieChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0"><i class="mdi mdi-chart-bar text-primary mr-2"></i>Daily Appointments</h4>
                        <small class="text-muted">Last 7 days</small>
                    </div>
                    <canvas id="dailyChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card chart-card">
                <div class="card-body">
                    <h4 class="card-title"><i class="mdi mdi-trophy text-warning mr-2"></i>Top Services</h4>
                    @if($topServices->count())
                        @php $maxRev = $topServices->max('revenue'); @endphp
                        @foreach($topServices as $i => $svc)
                            <div class="top-service-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="font-weight-bold small">{{ $i + 1 }}. {{ \Illuminate\Support\Str::limit($svc->description, 30) }}</span>
                                    <span class="text-success small font-weight-bold">RM {{ number_format($svc->revenue, 2) }}</span>
                                </div>
                                <div class="progress" style="height:6px">
                                    <div class="progress-bar bg-success" style="width: {{ $maxRev > 0 ? ($svc->revenue / $maxRev) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center mt-4 mb-4"><i class="mdi mdi-chart-bar-stacked" style="font-size:48px;opacity:0.3"></i><br>No data yet.</p>
                    @endif

                    @if($topDoctor)
                        <hr>
                        <small class="text-muted">⭐ Top Doctor This Month</small>
                        <p class="mb-0 font-weight-bold">Dr. {{ $topDoctor->name }} <span class="badge badge-warning ml-1">{{ $topDoctor->total_appointments }} appts</span></p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ACTIVITY Row --}}
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0"><i class="mdi mdi-calendar text-info mr-2"></i>Recent Appointments</h4>
                        <a href="{{ route('appointments.index') }}" class="text-primary small">View All <i class="mdi mdi-arrow-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <thead><tr class="text-muted small"><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentAppointments as $appointment)
                                    <tr>
                                        <td><strong class="small">{{ $appointment->patient->name }}</strong><br><small class="text-muted">{{ $appointment->patient->patient_id }}</small></td>
                                        <td class="small">Dr. {{ $appointment->doctor->user->name }}</td>
                                        <td class="small">{{ $appointment->appointment_date->format('d M') }}<br><small class="text-muted">{{ $appointment->start_time }}</small></td>
                                        <td>
                                            @php $apptColors = ['completed' => 'success', 'pending' => 'warning', 'confirmed' => 'info', 'cancelled' => 'danger', 'in_progress' => 'primary']; @endphp
                                            <span class="badge badge-{{ $apptColors[$appointment->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4"><i class="mdi mdi-calendar-blank" style="font-size:32px;opacity:0.3"></i><br><small>No appointments yet</small></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card chart-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0"><i class="mdi mdi-receipt text-success mr-2"></i>Recent Invoices</h4>
                        <a href="{{ route('invoices.index') }}" class="text-primary small">View All <i class="mdi mdi-arrow-right"></i></a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <thead><tr class="text-muted small"><th>Invoice</th><th>Patient</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentInvoices as $invoice)
                                    <tr>
                                        <td><a href="{{ route('invoices.show', $invoice) }}" class="font-weight-bold small">{{ $invoice->invoice_number }}</a></td>
                                        <td class="small">{{ $invoice->patient->name }}</td>
                                        <td class="small font-weight-bold">RM {{ number_format($invoice->total, 2) }}</td>
                                        <td>
                                            @php $invColors = ['paid' => 'success', 'issued' => 'info', 'partial' => 'warning', 'cancelled' => 'danger']; @endphp
                                            <span class="badge badge-{{ $invColors[$invoice->status] ?? 'secondary' }}">{{ ucfirst($invoice->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4"><i class="mdi mdi-receipt" style="font-size:32px;opacity:0.3"></i><br><small>No invoices yet</small></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="card mb-4" style="background:linear-gradient(135deg,#f8fafc 0%,#eff6ff 100%);border:none;">
        <div class="card-body">
            <h5 class="font-weight-bold mb-3"><i class="mdi mdi-flash text-warning mr-2"></i>Quick Actions</h5>
            <div class="quick-actions-grid">
                <a href="{{ route('walk-in-queue.index') }}" class="quick-action"><i class="mdi mdi-ticket-confirmation"></i><span>Queue</span></a>
                <a href="{{ route('consultations.index') }}" class="quick-action"><i class="mdi mdi-stethoscope"></i><span>Consultations</span></a>
                <a href="{{ route('prescriptions.index') }}" class="quick-action"><i class="mdi mdi-pill"></i><span>Prescriptions</span></a>
                <a href="{{ route('lab-reports.index') }}" class="quick-action"><i class="mdi mdi-flask"></i><span>Lab Reports</span></a>
                <a href="{{ route('invoices.index') }}" class="quick-action"><i class="mdi mdi-receipt"></i><span>Billing</span></a>
                <a href="{{ route('insurance-claims.index') }}" class="quick-action"><i class="mdi mdi-shield-check"></i><span>Claims</span></a>
                <a href="{{ route('leads.index') }}" class="quick-action"><i class="mdi mdi-account-search"></i><span>Leads CRM</span></a>
                <a href="{{ route('reports.index') }}" class="quick-action"><i class="mdi mdi-chart-bar"></i><span>Reports</span></a>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Hero Banner */
        .dashboard-hero {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(14, 165, 233, 0.2);
        }
        .dashboard-hero-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #6366f1 0%, #0ea5e9 50%, #06b6d4 100%);
            z-index: 0;
        }
        .dashboard-hero-bg::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .dashboard-hero-bg::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: 100px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        .dashboard-hero-content {
            position: relative;
            z-index: 1;
            padding: 30px 32px;
        }
        .opacity-90 { opacity: 0.9; }
        .hero-stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .hero-stat {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 14px 16px;
            text-align: center;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .hero-stat-num {
            font-size: 1.8em;
            font-weight: 800;
            line-height: 1;
        }
        .hero-stat-label {
            font-size: 0.75em;
            opacity: 0.9;
            margin-top: 4px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        /* KPI Cards (big) */
        .kpi-card {
            display: flex;
            align-items: center;
            padding: 22px 20px;
            border-radius: 16px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-height: 130px;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 130px;
            height: 130px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        .kpi-card .kpi-icon {
            font-size: 2.6em;
            margin-right: 16px;
            opacity: 0.9;
            z-index: 1;
        }
        .kpi-card .kpi-body { z-index: 1; flex: 1; }
        .kpi-card .kpi-label {
            font-size: 0.78em;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .kpi-card .kpi-value {
            font-size: 1.9em;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 6px;
        }
        .kpi-card .kpi-trend {
            font-size: 0.72em;
            opacity: 0.85;
        }
        .kpi-card .kpi-trend i {
            font-size: 1.1em;
            vertical-align: middle;
        }
        .kpi-blue { background: linear-gradient(135deg, #3b82f6, #6366f1); }
        .kpi-cyan { background: linear-gradient(135deg, #06b6d4, #0ea5e9); }
        .kpi-green { background: linear-gradient(135deg, #10b981, #059669); }
        .kpi-purple { background: linear-gradient(135deg, #a855f7, #ec4899); }

        /* Mini KPI tiles */
        .mini-kpi {
            background: #fff;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            border-top: 3px solid;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
            color: #1f2937;
        }
        .mini-kpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }
        .mini-kpi i {
            font-size: 1.6em;
            margin-bottom: 6px;
            display: block;
        }
        .mini-kpi-num {
            font-size: 1.5em;
            font-weight: 800;
            line-height: 1;
        }
        .mini-kpi-label {
            font-size: 0.7em;
            color: #6b7280;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .mini-kpi-info { border-top-color: #06b6d4; }
        .mini-kpi-info i { color: #06b6d4; }
        .mini-kpi-success { border-top-color: #10b981; }
        .mini-kpi-success i { color: #10b981; }
        .mini-kpi-danger { border-top-color: #ef4444; }
        .mini-kpi-danger i { color: #ef4444; }
        .mini-kpi-warning { border-top-color: #f59e0b; }
        .mini-kpi-warning i { color: #f59e0b; }
        .mini-kpi-pink { border-top-color: #ec4899; }
        .mini-kpi-pink i { color: #ec4899; }
        .mini-kpi-orange { border-top-color: #f97316; }
        .mini-kpi-orange i { color: #f97316; }
        .mini-kpi-teal { border-top-color: #14b8a6; }
        .mini-kpi-teal i { color: #14b8a6; }

        /* Chart card */
        .chart-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }
        .chart-card .card-title {
            font-weight: 700;
            font-size: 1em;
        }

        /* Top services */
        .top-service-item {
            margin-bottom: 12px;
        }
        .top-service-item:last-child { margin-bottom: 0; }

        /* Quick actions */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 12px;
        }
        .quick-action {
            background: #fff;
            border-radius: 12px;
            padding: 16px 8px;
            text-align: center;
            text-decoration: none !important;
            color: #475569;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }
        .quick-action:hover {
            transform: translateY(-3px);
            color: #0ea5e9;
            border-color: #0ea5e9;
            box-shadow: 0 6px 16px rgba(14, 165, 233, 0.15);
        }
        .quick-action i {
            font-size: 1.6em;
            display: block;
            margin-bottom: 6px;
        }
        .quick-action span {
            font-size: 0.78em;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .quick-actions-grid { grid-template-columns: repeat(4, 1fr); }
            .kpi-card { min-height: auto; padding: 16px; }
            .kpi-card .kpi-value { font-size: 1.5em; }
            .dashboard-hero-content { padding: 20px; }
            .hero-stat-grid { margin-top: 16px; }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Revenue line chart
        var revenueCtx = document.getElementById('revenueChart').getContext('2d');
        var gradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueMonths->pluck('label')) !!},
                datasets: [{
                    label: 'Revenue (RM)',
                    data: {!! json_encode($revenueMonths->pluck('total')) !!},
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, callback: function(v) { return 'RM ' + v.toLocaleString(); }, fontColor: '#94a3b8' },
                        gridLines: { color: '#f1f5f9', drawBorder: false }
                    }],
                    xAxes: [{
                        ticks: { fontColor: '#94a3b8' },
                        gridLines: { display: false }
                    }]
                },
                tooltips: {
                    backgroundColor: '#1e293b',
                    titleFontSize: 13,
                    bodyFontSize: 12,
                    cornerRadius: 8,
                    callbacks: { label: function(t) { return 'RM ' + Number(t.yLabel).toLocaleString(undefined, {minimumFractionDigits:2}); } }
                }
            }
        });

        // Appointment doughnut
        var pieCtx = document.getElementById('appointmentPieChart').getContext('2d');
        var apptData = @json($appointmentStats);
        var pieColors = { completed: '#10b981', pending: '#f59e0b', confirmed: '#0ea5e9', cancelled: '#ef4444', 'no_show': '#94a3b8', in_progress: '#6366f1' };
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(apptData).map(function(s) { return s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '); }),
                datasets: [{
                    data: Object.values(apptData),
                    backgroundColor: Object.keys(apptData).map(function(s) { return pieColors[s] || '#94a3b8'; }),
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutoutPercentage: 70,
                legend: { position: 'bottom', labels: { fontColor: '#475569', fontSize: 11, padding: 10, boxWidth: 12 } },
                tooltips: { backgroundColor: '#1e293b', cornerRadius: 8 }
            }
        });

        // Daily bar chart
        var dailyCtx = document.getElementById('dailyChart').getContext('2d');
        var dailyGradient = dailyCtx.createLinearGradient(0, 0, 0, 300);
        dailyGradient.addColorStop(0, '#6366f1');
        dailyGradient.addColorStop(1, '#0ea5e9');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyAppointments->pluck('label')) !!},
                datasets: [{
                    label: 'Appointments',
                    data: {!! json_encode($dailyAppointments->pluck('count')) !!},
                    backgroundColor: dailyGradient,
                    borderRadius: 8,
                    barPercentage: 0.5,
                    hoverBackgroundColor: '#6366f1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        ticks: { beginAtZero: true, stepSize: 1, fontColor: '#94a3b8' },
                        gridLines: { color: '#f1f5f9', drawBorder: false }
                    }],
                    xAxes: [{
                        ticks: { fontColor: '#94a3b8' },
                        gridLines: { display: false }
                    }]
                },
                tooltips: { backgroundColor: '#1e293b', cornerRadius: 8 }
            }
        });
    </script>
    @endpush
</x-app-layout>
