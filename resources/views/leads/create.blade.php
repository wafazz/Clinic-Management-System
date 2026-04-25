<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Lead</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('leads.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group"><label>Name *</label><input type="text" name="name" required class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Phone *</label><input type="text" name="phone" required class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Email</label><input type="email" name="email" class="form-control" /></div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group"><label>IC Number</label><input type="text" name="ic_number" class="form-control" /></div>
                <div class="col-md-2 form-group"><label>Gender</label>
                    <select name="gender" class="form-control"><option value="">-</option><option value="male">Male</option><option value="female">Female</option></select>
                </div>
                <div class="col-md-3 form-group"><label>Date of Birth</label><input type="date" name="date_of_birth" class="form-control" /></div>
                <div class="col-md-4 form-group"><label>Source</label><input type="text" name="source" class="form-control" placeholder="Facebook, walk-in, referral..." /></div>
            </div>
            <div class="row">
                <div class="col-md-6 form-group"><label>Service Interest</label><input type="text" name="service_interest" class="form-control" /></div>
                <div class="col-md-6 form-group"><label>Assign To</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">Unassigned</option>
                        @foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }} ({{ ucfirst($u->role) }})</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('leads.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create Lead</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
