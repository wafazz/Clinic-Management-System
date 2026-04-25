<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Lab Report</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('lab-reports.store') }}" >
                @csrf
                <div class="row">
                    <div>
                        <label class="form-label">Patient *</label>
                        <select name="patient_id" required class="form-control">
                            <option value="">Select Patient</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->patient_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Referring Doctor *</label>
                        <select name="doctor_id" required class="form-control">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Select Tests *</label>
                    <div class="row">
                        @foreach($labTests as $test)
                            <label class="d-flex align-items-center gap-1">
                                <input type="checkbox" name="tests[]" value="{{ $test->id }}" {{ in_array($test->id, old('tests', [])) ? 'checked' : '' }} class="form-check-input" />
                                <span class="text-sm">{{ $test->name }} <span class="text-muted">(RM {{ number_format($test->price, 2) }})</span></span>
                            </label>
                        @endforeach
                    </div>
                    @error('tests') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Create Report</button>
                    <a href="{{ route('lab-reports.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
