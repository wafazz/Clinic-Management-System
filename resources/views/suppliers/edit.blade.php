<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Supplier</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
            @csrf @method('PUT')
            <div class="form-group"><label>Name *</label><input type="text" name="name" required class="form-control" value="{{ old('name', $supplier->name) }}" /></div>
            <div class="row">
                <div class="col-md-6 form-group"><label>Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}" /></div>
                <div class="col-md-6 form-group"><label>Phone *</label><input type="text" name="phone" required class="form-control" value="{{ old('phone', $supplier->phone) }}" /></div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}" /></div>
                <div class="col-md-6 form-group"><label>Registration No.</label><input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $supplier->registration_number) }}" /></div>
            </div>
            <div class="form-group"><label>Address</label><textarea name="address" rows="2" class="form-control">{{ old('address', $supplier->address) }}</textarea></div>
            <div class="form-check mb-3"><input type="checkbox" name="is_active" value="1" id="active" class="form-check-input" {{ $supplier->is_active ? 'checked' : '' }}><label for="active" class="form-check-label">Active</label></div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-light">Cancel</a>
        </form>
    </div></div>
</x-app-layout>
