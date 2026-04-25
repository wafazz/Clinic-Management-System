<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Book Appointment</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('appointments.store') }}" >
                @csrf
                <div class="row">
                    <div>
                        <label class="form-label">Patient *</label>
                        <select name="patient_id" required class="form-control">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $selectedPatient) == $patient->id ? 'selected' : '' }}>{{ $patient->patient_id }} - {{ $patient->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Doctor *</label>
                        <select name="doctor_id" required class="form-control">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>Dr. {{ $doc->user->name }} ({{ $doc->specialization ?? 'GP' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Date *</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', date('Y-m-d')) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Start Time *</label>
                        <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">End Time *</label>
                        <input type="time" name="end_time" value="{{ old('end_time', '09:30') }}" required class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Reason</label>
                    <textarea name="reason" rows="2" class="form-control">{{ old('reason') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Book Appointment</button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
