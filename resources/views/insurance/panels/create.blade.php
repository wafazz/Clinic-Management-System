<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add Insurance Panel</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('insurance-panels.store') }}" >
                @csrf
                <div class="row">
                    <div>
                        <label class="form-label">Company Name *</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" required class="form-control" />
                        @error('company_name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">Type *</label>
                        <select name="type" required class="form-control">
                            @foreach(['corporate' => 'Corporate Panel', 'insurance' => 'Insurance Company', 'tpa' => 'TPA (Third Party Administrator)', 'government' => 'Government'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address') }}</textarea>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Credit Terms (days) *</label>
                        <input type="number" name="credit_terms" value="{{ old('credit_terms', 30) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Per Visit Limit (RM)</label>
                        <input type="number" step="0.01" name="consultation_limit" value="{{ old('consultation_limit') }}" class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Annual Limit (RM)</label>
                        <input type="number" step="0.01" name="annual_limit" value="{{ old('annual_limit') }}" class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Covered Services</label>
                    <textarea name="covered_services" rows="2" class="form-control" placeholder="e.g., Outpatient consultation, lab tests, medications">{{ old('covered_services') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Exclusions</label>
                    <textarea name="exclusions" rows="2" class="form-control" placeholder="e.g., Dental, optical, cosmetic">{{ old('exclusions') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="d-flex align-items-center">
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="requires_gl" value="1" {{ old('requires_gl') ? 'checked' : '' }} class="form-check-input" />
                        <span class="ml-2 text-sm">Requires Guarantee Letter (GL)</span>
                    </label>
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="form-check-input" />
                        <span class="ml-2 text-sm">Active</span>
                    </label>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Create Panel</button>
                    <a href="{{ route('insurance-panels.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
