<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit: {{ $medicine->name }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('medicines.update', $medicine) }}" >
                @csrf @method('PUT')
                <div class="row">
                    <div>
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" value="{{ old('name', $medicine->name) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Generic Name</label>
                        <input type="text" name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Category</label>
                        <select name="pharmacy_category_id" class="form-control">
                            <option value="">None</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('pharmacy_category_id', $medicine->pharmacy_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $medicine->sku) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Unit *</label>
                        <select name="unit" required class="form-control">
                            @foreach(['tablet', 'capsule', 'ml', 'bottle', 'tube', 'sachet', 'vial', 'ampoule', 'piece'] as $u)
                                <option value="{{ $u }}" {{ old('unit', $medicine->unit) === $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Cost Price (RM) *</label>
                        <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $medicine->cost_price) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Selling Price (RM) *</label>
                        <input type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $medicine->selling_price) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Reorder Level</label>
                        <input type="number" name="reorder_level" value="{{ old('reorder_level', $medicine->reorder_level) }}" class="form-control" />
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $medicine->expiry_date?->format('Y-m-d')) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Manufacturer</label>
                        <input type="text" name="manufacturer" value="{{ old('manufacturer', $medicine->manufacturer) }}" class="form-control" />
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $medicine->is_active ? 'checked' : '' }} class="form-check-input" />
                    <label class="ml-2 text-sm">Active</label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    <a href="{{ route('medicines.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
