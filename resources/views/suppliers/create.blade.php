<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Supplier</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            <div class="form-group"><label>Name *</label><input type="text" name="name" required class="form-control" value="{{ old('name') }}" /></div>
            <div class="row">
                <div class="col-md-6 form-group"><label>Contact Person</label><input type="text" name="contact_person" class="form-control" value="{{ old('contact_person') }}" /></div>
                <div class="col-md-6 form-group"><label>Phone *</label><input type="text" name="phone" required class="form-control" value="{{ old('phone') }}" /></div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group"><label>Email</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" /></div>
                <div class="col-md-6 form-group"><label>Registration No.</label><input type="text" name="registration_number" class="form-control" value="{{ old('registration_number') }}" /></div>
            </div>
            <div class="form-group"><label>Address</label><textarea name="address" rows="2" class="form-control">{{ old('address') }}</textarea></div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('suppliers.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
