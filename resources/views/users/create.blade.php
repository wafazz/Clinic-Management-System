<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add User</h4></x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" required class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label class="form-label">Role *</label>
                        <select name="role" required class="form-control">
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-control">
                            <option value="">-- No Branch --</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="form-check-input" />
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Create User</button>
                <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</x-app-layout>
