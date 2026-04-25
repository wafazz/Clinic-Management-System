<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Schedule Locum Session</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('locum-sessions.store') }}" >
                @csrf
                <div class="row">
                    <div>
                        <label class="form-label">Locum Doctor *</label>
                        <select name="locum_doctor_id" required class="form-control">
                            <option value="">Select Doctor</option>
                            @foreach($locumDoctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('locum_doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Branch *</label>
                        <select name="branch_id" required class="form-control">
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', session('current_branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div>
                        <label class="form-label">Date *</label>
                        <input type="date" name="session_date" value="{{ old('session_date', date('Y-m-d')) }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">Start Time *</label>
                        <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required class="form-control" />
                    </div>
                    <div>
                        <label class="form-label">End Time *</label>
                        <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" required class="form-control" />
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Schedule Session</button>
                    <a href="{{ route('locum-sessions.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
