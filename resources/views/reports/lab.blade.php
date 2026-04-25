<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Lab Report Analytics</h4>
            <div>
                <a href="{{ route('reports.export.lab', request()->query()) }}" class="btn btn-success btn-sm"><i class="mdi mdi-file-excel"></i> Export CSV</a>
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
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #6c63ff;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Total Reports</p>
                    <h4 class="font-weight-bold mb-0">{{ $totalReports }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Completed</p>
                    <h4 class="font-weight-bold mb-0 text-success">{{ $completedReports }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Pending</p>
                    <h4 class="font-weight-bold mb-0 text-warning">{{ $pendingReports }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daily Volume</h5>
                    <canvas id="dailyVolumeChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status Breakdown</h5>
                    @if(collect($statusBreakdown)->sum() > 0)
                        <canvas id="statusChart" height="180"></canvas>
                    @else
                        <p class="text-muted text-center mt-4">No data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top Tests --}}
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Most Ordered Tests</h5>
                    <table class="table table-sm table-hover">
                        <thead><tr><th>Test Name</th><th class="text-right">Total Ordered</th><th class="text-right">Abnormal Results</th><th class="text-right">Abnormal %</th></tr></thead>
                        <tbody>
                            @forelse($topTests as $test)
                                <tr>
                                    <td>{{ $test->name }}</td>
                                    <td class="text-right">{{ $test->total }}</td>
                                    <td class="text-right {{ $test->abnormal_count > 0 ? 'text-danger font-weight-bold' : '' }}">{{ $test->abnormal_count }}</td>
                                    <td class="text-right">{{ $test->total > 0 ? round(($test->abnormal_count / $test->total) * 100, 1) : 0 }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">No test data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        new Chart(document.getElementById('dailyVolumeChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyVolume->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!},
                datasets: [{
                    label: 'Lab Reports',
                    data: {!! json_encode($dailyVolume->pluck('total')) !!},
                    backgroundColor: '#dc3545',
                    barPercentage: 0.6
                }]
            },
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } }
        });

        @if(collect($statusBreakdown)->sum() > 0)
        var labStatus = @json($statusBreakdown);
        var labColors = { completed: '#28a745', pending: '#ffc107', in_progress: '#17a2b8' };
        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(labStatus).map(function(s) { return s.replace('_', ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }); }),
                datasets: [{
                    data: Object.values(labStatus),
                    backgroundColor: Object.keys(labStatus).map(function(s) { return labColors[s] || '#6c757d'; }),
                    borderWidth: 2
                }]
            },
            options: { responsive: true, cutoutPercentage: 60 }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
