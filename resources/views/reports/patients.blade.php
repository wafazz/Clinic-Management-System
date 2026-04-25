<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Patient Report</h4>
            <div>
                <a href="{{ route('reports.export.patients', request()->query()) }}" class="btn btn-success btn-sm"><i class="mdi mdi-file-excel"></i> Export CSV</a>
                <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm">Back to Reports</a>
            </div>
        </div>
    </x-slot>

    {{-- Filter --}}
    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="d-flex align-items-center gap-2">
                <select name="branch_id" class="form-control form-control-sm" style="max-width:180px;">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #6c63ff;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Total Patients</p>
                    <h4 class="font-weight-bold mb-0">{{ number_format($totalPatients) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">New This Month</p>
                    <h4 class="font-weight-bold mb-0 text-success">{{ $newThisMonth }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #17a2b8;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Active Patients</p>
                    <h4 class="font-weight-bold mb-0">{{ number_format($activePatients) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Insured Patients</p>
                    <h4 class="font-weight-bold mb-0">{{ $insuredCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">New Patients (Last 6 Months)</h5>
                    <canvas id="monthlyNewChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gender Distribution</h5>
                    <canvas id="genderChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Blood Type Distribution</h5>
                    @if($bloodDist->count())
                        <canvas id="bloodChart" height="200"></canvas>
                    @else
                        <p class="text-muted text-center mt-4">No blood type data recorded.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Patients by Visits</h5>
                    <table class="table table-sm table-hover">
                        <thead><tr><th>#</th><th>Patient</th><th>IC</th><th class="text-right">Visits</th></tr></thead>
                        <tbody>
                            @forelse($topPatients as $i => $p)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td><a href="{{ route('patients.show', $p) }}">{{ $p->name }}</a></td>
                                    <td>{{ $p->ic_number }}</td>
                                    <td class="text-right font-weight-bold">{{ $p->appointments_count }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        new Chart(document.getElementById('monthlyNewChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyNew->pluck('label')) !!},
                datasets: [{
                    label: 'New Patients',
                    data: {!! json_encode($monthlyNew->pluck('count')) !!},
                    backgroundColor: '#6c63ff',
                    barPercentage: 0.6
                }]
            },
            options: { responsive: true, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } }
        });

        var genderData = @json($genderDist);
        var genderColors = { male: '#17a2b8', female: '#e83e8c', other: '#6c757d' };
        new Chart(document.getElementById('genderChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(genderData).map(function(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : 'Unknown'; }),
                datasets: [{
                    data: Object.values(genderData),
                    backgroundColor: Object.keys(genderData).map(function(s) { return genderColors[s] || '#6c757d'; }),
                    borderWidth: 2
                }]
            },
            options: { responsive: true, cutoutPercentage: 60 }
        });

        @if($bloodDist->count())
        new Chart(document.getElementById('bloodChart').getContext('2d'), {
            type: 'horizontalBar',
            data: {
                labels: {!! json_encode($bloodDist->keys()) !!},
                datasets: [{
                    label: 'Patients',
                    data: {!! json_encode($bloodDist->values()) !!},
                    backgroundColor: '#dc3545',
                    barPercentage: 0.6
                }]
            },
            options: { responsive: true, scales: { xAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
