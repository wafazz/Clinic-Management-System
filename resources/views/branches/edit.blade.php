<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Branch - {{ $branch->name }}</h4></x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('branches.update', $branch) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name', $branch->name) }}" required class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Code *</label>
                        <input type="text" name="code" value="{{ old('code', $branch->code) }}" required class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $branch->address) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $branch->email) }}" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Opening Time</label>
                        <input type="time" name="opening_time" value="{{ old('opening_time', $branch->opening_time) }}" class="form-control" />
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Closing Time</label>
                        <input type="time" name="closing_time" value="{{ old('closing_time', $branch->closing_time) }}" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0" />
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} class="form-check-input" />
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Update Branch</button>
                <a href="{{ route('branches.index') }}" class="btn btn-light">Cancel</a>
            </form>
        </div>
    </div>
</x-app-layout>
