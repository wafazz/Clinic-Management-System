<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Appointment #{{ $appointment->id }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('appointments.update', $appointment) }}" >
                @csrf @method('PUT')
                <div class="row">
                    <div>
                        <label class="form-label">Patient *</label>
                        <select name="patient_id" required class="form-control">
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>{{ $patient->patient_id }} - {{ $patient->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Doctor *</label>
                        <select name="doctor_id" required class="form-control">
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doc->id ? 'selected' : '' }}>Dr. {{ $doc->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Date *</label>
                        <input type="date" name="appointment_date" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Start Time *</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $appointment->start_time) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">End Time *</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $appointment->end_time) }}" required class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Reason</label>
                    <textarea name="reason" rows="2" class="form-control">{{ old('reason', $appointment->reason) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $appointment->notes) }}</textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    <a href="{{ route('appointments.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
