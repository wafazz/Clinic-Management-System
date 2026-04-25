<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Lab Test: {{ $labTest->name }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('lab-tests.update', $labTest) }}" >
                @csrf @method('PUT')
                <div>
                    <label class="form-label">Test Name *</label>
                    <input type="text" name="name" value="{{ old('name', $labTest->name) }}" required class="form-control" />
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <input type="text" name="category" value="{{ old('category', $labTest->category) }}" class="form-control" />
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Normal Range</label>
                        <input type="text" name="normal_range" value="{{ old('normal_range', $labTest->normal_range) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit', $labTest->unit) }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Price (RM) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $labTest->price) }}" required class="form-control" />
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $labTest->description) }}</textarea>
                </div>
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $labTest->is_active ? 'checked' : '' }} class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    <a href="{{ route('lab-tests.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
