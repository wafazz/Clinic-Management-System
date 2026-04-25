<div class="row">
    <div class="col-md-6 form-group"><label>Name *</label><input type="text" name="name" required class="form-control" value="{{ old('name', $tier?->name) }}" /></div>
    <div class="col-md-3 form-group"><label>Price *</label><input type="number" step="0.01" name="price" required class="form-control" value="{{ old('price', $tier?->price ?? 0) }}" /></div>
    <div class="col-md-3 form-group"><label>Billing Cycle *</label>
        <select name="billing_cycle" class="form-control">
            @foreach(['free','monthly','yearly'] as $c)<option value="{{ $c }}" {{ old('billing_cycle', $tier?->billing_cycle) === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>@endforeach
        </select>
    </div>
</div>
<div class="form-group"><label>Description</label><textarea name="description" rows="2" class="form-control">{{ old('description', $tier?->description) }}</textarea></div>

<h6 class="mt-3">Discounts (%)</h6>
<div class="row">
    <div class="col-md-4 form-group"><label>Consultation</label><input type="number" step="0.01" name="discount_consultation" class="form-control" value="{{ old('discount_consultation', $tier?->discount_consultation ?? 0) }}" /></div>
    <div class="col-md-4 form-group"><label>Medicine</label><input type="number" step="0.01" name="discount_medicine" class="form-control" value="{{ old('discount_medicine', $tier?->discount_medicine ?? 0) }}" /></div>
    <div class="col-md-4 form-group"><label>Lab</label><input type="number" step="0.01" name="discount_lab" class="form-control" value="{{ old('discount_lab', $tier?->discount_lab ?? 0) }}" /></div>
</div>

<h6 class="mt-3">Free Per Year</h6>
<div class="row">
    <div class="col-md-4 form-group"><label>Consultations</label><input type="number" name="free_consultations_per_year" class="form-control" value="{{ old('free_consultations_per_year', $tier?->free_consultations_per_year ?? 0) }}" /></div>
    <div class="col-md-4 form-group"><label>Lab Tests</label><input type="number" name="free_lab_tests_per_year" class="form-control" value="{{ old('free_lab_tests_per_year', $tier?->free_lab_tests_per_year ?? 0) }}" /></div>
    <div class="col-md-4 form-group"><label>Max Family Members</label><input type="number" name="max_family_members" class="form-control" value="{{ old('max_family_members', $tier?->max_family_members ?? 0) }}" /></div>
</div>
<div class="form-check mb-3"><input type="checkbox" name="priority_queue" value="1" id="pq" class="form-check-input" {{ old('priority_queue', $tier?->priority_queue) ? 'checked' : '' }}><label for="pq" class="form-check-label">Priority Queue</label></div>
