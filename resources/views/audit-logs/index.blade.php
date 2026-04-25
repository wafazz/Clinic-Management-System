<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Audit Logs</h4>
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row align-items-end">
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Branch</label>
                    <select name="branch_id" class="form-control form-control-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id', $branchId) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">User</label>
                    <select name="user_id" class="form-control form-control-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-2">
                    <label class="small font-weight-bold">Action</label>
                    <select name="action" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Module</label>
                    <select name="model_type" class="form-control form-control-sm">
                        <option value="">All Modules</option>
                        @foreach($modelTypes as $type)
                            <option value="{{ $type }}" {{ request('model_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-2">
                    <label class="small font-weight-bold">From</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-md-1 mb-2">
                    <label class="small font-weight-bold">To</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-1 mb-2">
                    <button type="submit" class="btn btn-primary btn-sm btn-block">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Module</th>
                            <th>Description</th>
                            <th>Branch</th>
                            <th>IP</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td class="small">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td><span class="badge {{ $log->action_badge }}">{{ ucfirst($log->action) }}</span></td>
                                <td>{{ $log->model_name }}</td>
                                <td class="small">{{ Str::limit($log->description, 60) }}</td>
                                <td>{{ $log->branch->name ?? '-' }}</td>
                                <td class="small">{{ $log->ip_address }}</td>
                                <td>
                                    <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-outline-info btn-sm py-0 px-2">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No audit logs found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
