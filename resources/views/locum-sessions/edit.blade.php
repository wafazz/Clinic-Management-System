<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Locum Session</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('locum-sessions.update', $locumSession) }}" >
                @csrf @method('PUT')
                <div class="row">
                    <div>
                        <label class="form-label">Locum Doctor *</label>
                        <select name="locum_doctor_id" required class="form-control">
                            @foreach($locumDoctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('locum_doctor_id', $locumSession->locum_doctor_id) == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Branch *</label>
                        <select name="branch_id" required class="form-control">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $locumSession->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Date *</label>
                        <input type="date" name="session_date" value="{{ old('session_date', $locumSession->session_date->format('Y-m-d')) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Start Time *</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $locumSession->start_time) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">End Time *</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $locumSession->end_time) }}" required class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" required class="form-control">
                        @foreach(['scheduled','in_progress','completed','cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('status', $locumSession->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $locumSession->notes) }}</textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update Session</button>
                    <a href="{{ route('locum-sessions.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
