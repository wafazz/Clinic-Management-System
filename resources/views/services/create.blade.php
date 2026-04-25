<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add Service</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('services.store') }}" >
                @csrf
                <div>
                    <label class="form-label">Branch *</label>
                    <select name="branch_id" required class="form-control">
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', session('current_branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}" class="form-control" placeholder="e.g. Consultation, Lab, Procedure" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Price (RM) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', '0') }}" required class="form-control" />
                </div>
                <div class="d-flex align-items-center">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" value="1" checked class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Create Service</button>
                    <a href="{{ route('services.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
