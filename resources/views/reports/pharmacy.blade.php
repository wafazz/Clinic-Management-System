<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Pharmacy Report</h4>
            <div>
                <a href="{{ route('reports.export.pharmacy', request()->query()) }}" class="btn btn-success btn-sm"><i class="mdi mdi-file-excel"></i> Export CSV</a>
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
                    <p class="text-muted mb-0 small">Total Medicines</p>
                    <h4 class="font-weight-bold mb-0">{{ $totalMedicines }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #dc3545;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Low Stock Items</p>
                    <h4 class="font-weight-bold mb-0 {{ $lowStock->count() > 0 ? 'text-danger' : '' }}">{{ $lowStock->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #ffc107;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Expiring Soon (3mo)</p>
                    <h4 class="font-weight-bold mb-0 {{ $expiringSoon->count() > 0 ? 'text-warning' : '' }}">{{ $expiringSoon->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card" style="border-left:4px solid #28a745;">
                <div class="card-body py-3">
                    <p class="text-muted mb-0 small">Stock Value (Sell)</p>
                    <h4 class="font-weight-bold mb-0 text-success">RM {{ number_format($stockValue->sell_value ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Low Stock Alert --}}
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="mdi mdi-alert mr-1"></i>Low Stock Alert</h5>
                    @if($lowStock->count())
                        <table class="table table-sm">
                            <thead><tr><th>Medicine</th><th class="text-right">Stock</th><th class="text-right">Reorder Level</th></tr></thead>
                            <tbody>
                                @foreach($lowStock as $med)
                                    <tr>
                                        <td><a href="{{ route('medicines.show', $med) }}">{{ $med->name }}</a></td>
                                        <td class="text-right text-danger font-weight-bold">{{ $med->current_stock }} {{ $med->unit }}</td>
                                        <td class="text-right">{{ $med->reorder_level }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center mt-3">All stock levels are healthy.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Expiring Soon --}}
        <div class="col-lg-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-warning"><i class="mdi mdi-clock-alert mr-1"></i>Expiring Soon (3 Months)</h5>
                    @if($expiringSoon->count())
                        <table class="table table-sm">
                            <thead><tr><th>Medicine</th><th>Expiry Date</th><th class="text-right">Stock</th></tr></thead>
                            <tbody>
                                @foreach($expiringSoon as $med)
                                    <tr>
                                        <td>{{ $med->name }}</td>
                                        <td class="text-warning font-weight-bold">{{ $med->expiry_date->format('d M Y') }}</td>
                                        <td class="text-right">{{ $med->current_stock }} {{ $med->unit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center mt-3">No medicines expiring soon.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Top Dispensed --}}
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Top Dispensed (Last 30 Days)</h5>
                    @if($topDispensed->count())
                        <canvas id="topDispensedChart" height="150"></canvas>
                    @else
                        <p class="text-muted text-center mt-4">No dispensing data.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stock by Category --}}
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Stock by Category</h5>
                    @if($stockByCategory->count())
                        <table class="table table-sm">
                            <thead><tr><th>Category</th><th class="text-right">Items</th><th class="text-right">Total Stock</th></tr></thead>
                            <tbody>
                                @foreach($stockByCategory as $cat)
                                    <tr>
                                        <td>{{ $cat->name }}</td>
                                        <td class="text-right">{{ $cat->count }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($cat->total_stock) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted text-center mt-3">No data.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        @if($topDispensed->count())
        new Chart(document.getElementById('topDispensedChart').getContext('2d'), {
            type: 'horizontalBar',
            data: {
                labels: {!! json_encode($topDispensed->pluck('name')) !!},
                datasets: [{
                    label: 'Qty Dispensed',
                    data: {!! json_encode($topDispensed->pluck('total_qty')) !!},
                    backgroundColor: '#ffc107',
                    barPercentage: 0.6
                }]
            },
            options: { responsive: true, scales: { xAxes: [{ ticks: { beginAtZero: true } }] } }
        });
        @endif
    </script>
    @endpush
</x-app-layout>
