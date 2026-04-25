<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Appointment Report</h4>
            <div>
                <a href="{{ route('reports.export.appointments', request()->query()) }}" class="btn btn-success btn-sm"><i class="mdi mdi-file-excel"></i> Export CSV</a>
                <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm">Back to Reports</a>
            </div>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="d-flex align-items-center gap-2">
                <select name="branch_id" class="form-control form-control-sm" style="max-width:180px;">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" style="max-width:160px;" />
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" style="max-width:160px;" />
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #6c63ff;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Total Appointments</p>
                    <h4 class="font-weight-bold mb-0">{{ number_format($totalAppointments) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Completed</p>
                    <h4 class="font-weight-bold mb-0 text-success">{{ $completedCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #dc3545;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Cancelled</p>
                    <h4 class="font-weight-bold mb-0 text-danger">{{ $cancelledCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #17a2b8;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Completion Rate</p>
                    <h4 class="font-weight-bold mb-0">{{ $completionRate }}%</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daily Trend</h5>
                    <canvas id="dailyTrendChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status Breakdown</h5>
                    <canvas id="statusChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Peak Hours</h5>
                    <canvas id="peakHoursChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Day of Week</h5>
                    <canvas id="dayOfWeekChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Doctor Table --}}
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Appointments by Doctor</h5>
                    <table class="table table-sm table-hover">
                        <thead><tr><th>Doctor</th><th class="text-right">Total</th><th class="text-right">Completed</th><th class="text-right">Cancelled</th><th class="text-right">Rate</th></tr></thead>
                        <tbody>
                            @forelse($byDoctor as $row)
                                <tr>
                                    <td>Dr. {{ $row->name }}</td>
                                    <td class="text-right">{{ $row->total }}</td>
                                    <td class="text-right text-success">{{ $row->completed }}</td>
                                    <td class="text-right text-danger">{{ $row->cancelled }}</td>
                                    <td class="text-right font-weight-bold">{{ $row->total > 0 ? round(($row->completed / $row->total) * 100, 1) : 0 }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        new Chart(document.getElementById('dailyTrendChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyTrend->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!},
                datasets: [{
                    label: 'Appointments',
                    data: {!! json_encode($dailyTrend->pluck('total')) !!},
                    borderColor: '#6c63ff',
                    backgroundColor: 'rgba(108,99,255,0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } }
        });

        var statusData = @json($statusBreakdown);
        var statusColors = { completed: '#28a745', pending: '#ffc107', confirmed: '#17a2b8', cancelled: '#dc3545', 'no-show': '#6c757d' };
        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData).map(function(s) { return s.charAt(0).toUpperCase() + s.slice(1); }),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: Object.keys(statusData).map(function(s) { return statusColors[s] || '#6c757d'; }),
                    borderWidth: 2
                }]
            },
            options: { responsive: true, cutoutPercentage: 60 }
        });

        new Chart(document.getElementById('peakHoursChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($peakHours->pluck('hour')->map(fn($h) => sprintf('%02d:00', $h))) !!},
                datasets: [{
                    label: 'Appointments',
                    data: {!! json_encode($peakHours->pluck('total')) !!},
                    backgroundColor: '#17a2b8',
                    barPercentage: 0.7
                }]
            },
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } }
        });

        new Chart(document.getElementById('dayOfWeekChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($dayOfWeek->pluck('day_name')) !!},
                datasets: [{
                    label: 'Appointments',
                    data: {!! json_encode($dayOfWeek->pluck('total')) !!},
                    backgroundColor: '#ffc107',
                    barPercentage: 0.6
                }]
            },
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } }
        });
    </script>
    @endpush
</x-app-layout>
