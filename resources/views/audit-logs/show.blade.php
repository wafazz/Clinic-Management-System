<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Audit Log Detail</h4>
            <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Event Info</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="130">Action</th>
                            <td><span class="badge {{ $auditLog->action_badge }}">{{ ucfirst($auditLog->action) }}</span></td>
                        </tr>
                        <tr>
                            <th>Module</th>
                            <td>{{ $auditLog->model_name }}</td>
                        </tr>
                        <tr>
                            <th>Record ID</th>
                            <td>{{ $auditLog->model_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $auditLog->description }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $auditLog->user->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>Branch</th>
                            <td>{{ $auditLog->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Date/Time</th>
                            <td>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>IP Address</th>
                            <td>{{ $auditLog->ip_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td class="small text-muted">{{ Str::limit($auditLog->user_agent, 100) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Changes</h5>

                    @if($auditLog->action === 'updated' && $auditLog->old_values && $auditLog->new_values)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Old Value</th>
                                        <th>New Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLog->new_values as $field => $newVal)
                                        <tr>
                                            <td class="font-weight-bold">{{ Str::title(str_replace('_', ' ', $field)) }}</td>
                                            <td class="text-danger">{{ is_array($auditLog->old_values[$field] ?? null) ? json_encode($auditLog->old_values[$field]) : ($auditLog->old_values[$field] ?? '-') }}</td>
                                            <td class="text-success">{{ is_array($newVal) ? json_encode($newVal) : $newVal }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($auditLog->action === 'created' && $auditLog->new_values)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Field</th><th>Value</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLog->new_values as $field => $val)
                                        @if(!in_array($field, ['password', 'remember_token', 'portal_token', 'updated_at', 'created_at']))
                                            <tr>
                                                <td class="font-weight-bold">{{ Str::title(str_replace('_', ' ', $field)) }}</td>
                                                <td>{{ is_array($val) ? json_encode($val) : $val }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif($auditLog->action === 'deleted' && $auditLog->old_values)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Field</th><th>Value</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($auditLog->old_values as $field => $val)
                                        @if(!in_array($field, ['password', 'remember_token', 'portal_token', 'updated_at', 'created_at']))
                                            <tr>
                                                <td class="font-weight-bold">{{ Str::title(str_replace('_', ' ', $field)) }}</td>
                                                <td class="text-danger">{{ is_array($val) ? json_encode($val) : $val }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No detailed changes recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
