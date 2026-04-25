<x-app-layout>
    <x-slot name="header">
        <h4 class="font-weight-bold mb-0">Reports & Analytics</h4>
    </x-slot>

    <div class="row">
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-cash-multiple text-success" style="font-size:48px;"></i>
                    <h5 class="font-weight-bold mt-3">Financial Report</h5>
                    <p class="text-muted">Revenue, payments, outstanding bills, service breakdown</p>
                    <a href="{{ route('reports.financial') }}" class="btn btn-outline-success btn-sm">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-account-multiple text-primary" style="font-size:48px;"></i>
                    <h5 class="font-weight-bold mt-3">Patient Report</h5>
                    <p class="text-muted">Demographics, growth trends, insurance coverage</p>
                    <a href="{{ route('reports.patients') }}" class="btn btn-outline-primary btn-sm">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-calendar-clock text-info" style="font-size:48px;"></i>
                    <h5 class="font-weight-bold mt-3">Appointment Report</h5>
                    <p class="text-muted">Utilization, peak hours, completion rates</p>
                    <a href="{{ route('reports.appointments') }}" class="btn btn-outline-info btn-sm">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-pill text-warning" style="font-size:48px;"></i>
                    <h5 class="font-weight-bold mt-3">Pharmacy Report</h5>
                    <p class="text-muted">Stock levels, expiry alerts, top dispensed</p>
                    <a href="{{ route('reports.pharmacy') }}" class="btn btn-outline-warning btn-sm">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="mdi mdi-flask text-danger" style="font-size:48px;"></i>
                    <h5 class="font-weight-bold mt-3">Lab Report</h5>
                    <p class="text-muted">Test volume, turnaround, abnormal results</p>
                    <a href="{{ route('reports.lab') }}" class="btn btn-outline-danger btn-sm">View Report</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
