<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add Lab Test</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('lab-tests.store') }}" >
                @csrf
                <div>
                    <label class="form-label">Test Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control" />
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" class="form-control" placeholder="e.g., Blood, Urine, Imaging" />
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Normal Range</label>
                        <input type="text" name="normal_range" value="{{ old('normal_range') }}" class="form-control" placeholder="e.g., 70-100" />
                    </div>
                    <div>
                        <label class="form-label">Unit</label>
                        <input type="text" name="unit" value="{{ old('unit') }}" class="form-control" placeholder="e.g., mg/dL" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Price (RM) *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', '0.00') }}" required class="form-control" />
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Create</button>
                    <a href="{{ route('lab-tests.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
