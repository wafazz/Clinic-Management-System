<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Start New Consultation</h4></x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('consultations.start') }}">
                @csrf
                <div class="form-group">
                    <label>Patient <span class="text-danger">*</span></label>
                    <select name="patient_id" class="form-control" required>
                        <option value="">-- Select Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Doctor <span class="text-danger">*</span></label>
                    <select name="doctor_id" class="form-control" required>
                        <option value="">-- Select Doctor --</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">Dr. {{ $d->user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('consultations.index') }}" class="btn btn-light mr-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Start Consultation</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
