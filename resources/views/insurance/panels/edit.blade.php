<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit: {{ $insurancePanel->company_name }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('insurance-panels.update', $insurancePanel) }}" >
                @csrf @method('PUT')
                <div class="row">
                    <div>
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $insurancePanel->company_name) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Type *</label>
                        <select name="type" required class="form-control">
                            @foreach(['corporate' => 'Corporate Panel', 'insurance' => 'Insurance Company', 'tpa' => 'TPA', 'government' => 'Government'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $insurancePanel->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $insurancePanel->contact_person) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $insurancePanel->phone) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $insurancePanel->email) }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $insurancePanel->address) }}</textarea>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Credit Terms (days) *</label>
                        <input type="number" name="credit_terms" value="{{ old('credit_terms', $insurancePanel->credit_terms) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Per Visit Limit (RM)</label>
                        <input type="number" step="0.01" name="consultation_limit" value="{{ old('consultation_limit', $insurancePanel->consultation_limit) }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Annual Limit (RM)</label>
                        <input type="number" step="0.01" name="annual_limit" value="{{ old('annual_limit', $insurancePanel->annual_limit) }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Covered Services</label>
                    <textarea name="covered_services" rows="2" class="form-control">{{ old('covered_services', $insurancePanel->covered_services) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Exclusions</label>
                    <textarea name="exclusions" rows="2" class="form-control">{{ old('exclusions', $insurancePanel->exclusions) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $insurancePanel->notes) }}</textarea>
                </div>
                <div class="d-flex align-items-center">
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="requires_gl" value="1" {{ old('requires_gl', $insurancePanel->requires_gl) ? 'checked' : '' }} class="form-check-input" />
                        <span class="ml-2 text-sm">Requires GL</span>
                    </label>
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $insurancePanel->is_active) ? 'checked' : '' }} class="form-check-input" />
                        <span class="ml-2 text-sm">Active</span>
                    </label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    <a href="{{ route('insurance-panels.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
