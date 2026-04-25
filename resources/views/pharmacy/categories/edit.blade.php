<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Category: {{ $pharmacyCategory->name }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('pharmacy-categories.update', $pharmacyCategory) }}" >
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $pharmacyCategory->name) }}" required class="form-control" />
                    @error('name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $pharmacyCategory->description) }}</textarea>
                </div>
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $pharmacyCategory->is_active ? 'checked' : '' }} class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    <a href="{{ route('pharmacy-categories.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
