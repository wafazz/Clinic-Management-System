<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">User Management</h4>
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">Add User</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email..." class="form-control form-control-sm" style="max-width:250px" />
                <select name="role" class="form-control form-control-sm ml-2" style="max-width:160px">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="doctor" {{ request('role') === 'doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="receptionist" {{ request('role') === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm ml-2">Filter</button>
            </form>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr>
                        <th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Branch</th><th>Status</th><th>Actions</th>
                    </tr></thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td class="font-weight-bold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @php
                                        $roleBadge = match($user->role) {
                                            'admin' => 'danger',
                                            'doctor' => 'info',
                                            'receptionist' => 'warning',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $roleBadge }}">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td>{{ $user->branch->name ?? '-' }}</td>
                                <td><span class="badge badge-{{ $user->is_active ? 'success' : 'danger' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-warning btn-sm py-1 px-2">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $users->appends(request()->query())->links() }}</div>
        </div>
    </div>
</x-app-layout>
