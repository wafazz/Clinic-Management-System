<x-app-layout>
    <x-slot name="header">
        <h4 class="font-weight-bold mb-0">
            Dashboard
            @if($currentBranch)
                <small class="text-muted">- {{ $currentBranch->name }}</small>
            @endif
        </h4>
    </x-slot>

    {{-- KPI Cards Row 1 --}}
    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #6c63ff;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-account-multiple text-primary" style="font-size:36px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Total Patients</p>
                            <h4 class="font-weight-bold mb-0">{{ number_format($totalPatients) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #17a2b8;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-calendar-check text-info" style="font-size:36px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Today's Appointments</p>
                            <h4 class="font-weight-bold mb-0">{{ $todayAppointments }} <small class="text-success font-weight-normal">/ {{ $completedToday }} done</small></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-clock-alert text-warning" style="font-size:36px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Pending Appointments</p>
                            <h4 class="font-weight-bold mb-0">{{ $pendingAppointments }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-cash-multiple text-success" style="font-size:36px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Monthly Revenue</p>
                            <h4 class="font-weight-bold mb-0">RM {{ number_format($monthlyRevenue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KPI Cards Row 2 --}}
    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-stethoscope text-info" style="font-size:30px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Active Doctors</p>
                            <h5 class="font-weight-bold mb-0">{{ $totalDoctors }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-pill text-danger" style="font-size:30px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Low Stock Medicines</p>
                            <h5 class="font-weight-bold mb-0 {{ $lowStockMedicines > 0 ? 'text-danger' : '' }}">{{ $lowStockMedicines }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-shield-check text-warning" style="font-size:30px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Pending Claims</p>
                            <h5 class="font-weight-bold mb-0">{{ $pendingClaims }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><i class="mdi mdi-chart-line text-success" style="font-size:30px;"></i></div>
                        <div>
                            <p class="text-muted mb-0 small">Quick Links</p>
                            <a href="{{ route('reports.index') }}" class="small font-weight-bold">View Reports &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Revenue Trend (6 Months)</h4>
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Appointments This Month</h4>
                    <canvas id="appointmentPieChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daily Appointments (Last 7 Days)</h4>
                    <canvas id="dailyChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Top Services This Month</h4>
                    @if($topServices->count())
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead><tr><th>Service</th><th class="text-right">Revenue</th></tr></thead>
                                <tbody>
                                    @foreach($topServices as $svc)
                                        <tr>
                                            <td>{{ Str::limit($svc->description, 30) }}</td>
                                            <td class="text-right font-weight-bold">RM {{ number_format($svc->revenue, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mt-4">No data yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Tables --}}
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Appointments</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentAppointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->patient->name }}</td>
                                        <td>Dr. {{ $appointment->doctor->user->name }}</td>
                                        <td>{{ $appointment->appointment_date->format('d M Y') }}</td>
                                        <td>
                                            @php $apptColors = ['completed' => 'success', 'pending' => 'warning', 'confirmed' => 'info', 'cancelled' => 'danger']; @endphp
                                            <span class="badge badge-{{ $apptColors[$appointment->status] ?? 'secondary' }}">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">No recent appointments.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Recent Invoices</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Invoice #</th><th>Patient</th><th>Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentInvoices as $invoice)
                                    <tr>
                                        <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                                        <td>{{ $invoice->patient->name }}</td>
                                        <td>RM {{ number_format($invoice->total, 2) }}</td>
                                        <td>
                                            @php $invColors = ['paid' => 'success', 'issued' => 'info', 'partial' => 'warning']; @endphp
                                            <span class="badge badge-{{ $invColors[$invoice->status] ?? 'secondary' }}">{{ ucfirst($invoice->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">No recent invoices.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueMonths->pluck('label')) !!},
                datasets: [{
                    label: 'Revenue (RM)',
                    data: {!! json_encode($revenueMonths->pluck('total')) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40,167,69,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                scales: { yAxes: [{ ticks: { beginAtZero: true, callback: function(v) { return 'RM ' + v.toLocaleString(); } } }] },
                tooltips: { callbacks: { label: function(t) { return 'RM ' + Number(t.yLabel).toLocaleString(undefined, {minimumFractionDigits:2}); } } }
            }
        });

        var pieCtx = document.getElementById('appointmentPieChart').getContext('2d');
        var apptData = @json($appointmentStats);
        var pieColors = { completed: '#28a745', pending: '#ffc107', confirmed: '#17a2b8', cancelled: '#dc3545', 'no-show': '#6c757d' };
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(apptData).map(function(s) { return s.charAt(0).toUpperCase() + s.slice(1); }),
                datasets: [{
                    data: Object.values(apptData),
                    backgroundColor: Object.keys(apptData).map(function(s) { return pieColors[s] || '#6c757d'; }),
                    borderWidth: 2
                }]
            },
            options: { responsive: true, cutoutPercentage: 60 }
        });

        var dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyAppointments->pluck('label')) !!},
                datasets: [{
                    label: 'Appointments',
                    data: {!! json_encode($dailyAppointments->pluck('count')) !!},
                    backgroundColor: '#6c63ff',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] }
            }
        });
    </script>
    @endpush
</x-app-layout>
