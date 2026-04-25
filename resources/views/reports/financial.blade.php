<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Financial Report</h4>
            <div>
                <a href="{{ route('reports.export.financial', request()->query()) }}" class="btn btn-success btn-sm"><i class="mdi mdi-file-excel"></i> Export CSV</a>
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
            <div class="card" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Total Revenue</p>
                    <h4 class="font-weight-bold mb-0 text-success">RM {{ number_format($totalRevenue, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Outstanding</p>
                    <h4 class="font-weight-bold mb-0 text-warning">RM {{ number_format($totalOutstanding, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #17a2b8;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Total Invoices</p>
                    <h4 class="font-weight-bold mb-0">{{ $totalInvoices }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #6c63ff;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Paid Invoices</p>
                    <h4 class="font-weight-bold mb-0">{{ $paidCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daily Revenue</h5>
                    <canvas id="dailyRevenueChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Payment Methods</h5>
                    @if($paymentMethods->count())
                        <canvas id="paymentMethodChart" height="180"></canvas>
                    @else
                        <p class="text-muted text-center mt-4">No payment data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tables --}}
    <div class="row">
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Revenue by Branch</h5>
                    <table class="table table-sm">
                        <thead><tr><th>Branch</th><th class="text-right">Revenue</th></tr></thead>
                        <tbody>
                            @forelse($revenueByBranch as $row)
                                <tr>
                                    <td>{{ $row->name }}</td>
                                    <td class="text-right font-weight-bold">RM {{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted text-center">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Revenue by Doctor</h5>
                    <table class="table table-sm">
                        <thead><tr><th>Doctor</th><th class="text-right">Invoices</th><th class="text-right">Revenue</th></tr></thead>
                        <tbody>
                            @forelse($revenueByDoctor as $row)
                                <tr>
                                    <td>Dr. {{ $row->name }}</td>
                                    <td class="text-right">{{ $row->invoice_count }}</td>
                                    <td class="text-right font-weight-bold">RM {{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Revenue by Service</h5>
                    <table class="table table-sm table-hover">
                        <thead><tr><th>Service</th><th class="text-right">Qty</th><th class="text-right">Revenue</th></tr></thead>
                        <tbody>
                            @forelse($revenueByService as $row)
                                <tr>
                                    <td>{{ $row->description }}</td>
                                    <td class="text-right">{{ $row->qty }}</td>
                                    <td class="text-right font-weight-bold">RM {{ number_format($row->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted text-center">No data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        var dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dailyRevenue->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!},
                datasets: [{
                    label: 'Revenue (RM)',
                    data: {!! json_encode($dailyRevenue->pluck('total')) !!},
                    backgroundColor: '#28a745',
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                scales: { yAxes: [{ ticks: { beginAtZero: true, callback: function(v) { return 'RM ' + v.toLocaleString(); } } }] }
            }
        });

        @if($paymentMethods->count())
        var pmCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(pmCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($paymentMethods->pluck('method')->map(fn($m) => ucfirst($m))) !!},
                datasets: [{
                    data: {!! json_encode($paymentMethods->pluck('total')) !!},
                    backgroundColor: ['#28a745', '#17a2b8', '#ffc107', '#6c63ff', '#dc3545'],
                    borderWidth: 2
                }]
            },
            options: { responsive: true, cutoutPercentage: 60 }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
