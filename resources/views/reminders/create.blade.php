<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Schedule Reminder</h4>
            <a href="{{ route('reminders.index') }}" class="btn btn-light btn-sm">Back to Reminders</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('reminders.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Appointment *</label>
                    <select name="appointment_id" required class="form-control form-control-sm">
                        <option value="">Select Appointment</option>
                        @foreach($appointments as $appt)
                            <option value="{{ $appt->id }}" {{ old('appointment_id', $selectedAppointment) == $appt->id ? 'selected' : '' }}>
                                {{ $appt->patient->name }} - {{ $appt->appointment_date->format('d M Y') }} {{ $appt->start_time }}
                            </option>
                        @endforeach
                    </select>
                    @error('appointment_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Channel *</label>
                            <select name="channel" required class="form-control form-control-sm">
                                <option value="whatsapp" {{ old('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="sms" {{ old('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                            </select>
                            @error('channel') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Phone Number *</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="form-control form-control-sm" placeholder="60123456789" />
                            @error('phone_number') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Scheduled At *</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="form-control form-control-sm" />
                    @error('scheduled_at') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Message *</label>
                    <textarea name="message" rows="3" required class="form-control form-control-sm">{{ old('message', 'Hi, this is a reminder for your upcoming appointment. Please arrive 10 minutes early. Thank you.') }}</textarea>
                    @error('message') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-success btn-sm"><i class="mdi mdi-send mr-1"></i>Schedule Reminder</button>
                    <a href="{{ route('reminders.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
