<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Add Walk-In Patient</h4></x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('walk-in-queue.store') }}" x-data="walkInForm()">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Existing Patient <small class="text-muted">(optional - select to auto-fill)</small></label>
                            <select name="patient_id" class="form-control" x-model="patientId" @change="fillPatient()">
                                <option value="">-- New / Unregistered Patient --</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-phone="{{ $p->phone }}">{{ $p->patient_id }} - {{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Doctor <small class="text-muted">(optional)</small></label>
                            <select name="doctor_id" class="form-control">
                                <option value="">-- Any Available Doctor --</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}">Dr. {{ $doc->user->name }} ({{ $doc->specialization ?? 'General' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Patient Name <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" class="form-control @error('patient_name') is-invalid @enderror" x-model="patientName" required>
                            @error('patient_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="patient_phone" class="form-control" x-model="patientPhone">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Reason for Visit</label>
                    <input type="text" name="reason" class="form-control" placeholder="e.g. Fever, Follow-up, Check-up">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('walk-in-queue.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-ticket mr-1"></i>Get Nombor Giliran</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function walkInForm() {
        return {
            patientId: '',
            patientName: '',
            patientPhone: '',
            fillPatient() {
                if (this.patientId) {
                    let option = document.querySelector(`select[name="patient_id"] option[value="${this.patientId}"]`);
                    this.patientName = option.dataset.name || '';
                    this.patientPhone = option.dataset.phone || '';
                } else {
                    this.patientName = '';
                    this.patientPhone = '';
                }
            }
        }
    }
    </script>
    @endpush
</x-app-layout>
