<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Lead — {{ $lead->name }}</h4></x-slot>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-body">
                <h5>{{ $lead->name }}</h5>
                <p><strong>Phone:</strong> {{ $lead->phone }}</p>
                <p><strong>Email:</strong> {{ $lead->email ?? '-' }}</p>
                <p><strong>IC:</strong> {{ $lead->ic_number ?? '-' }}</p>
                <p><strong>Source:</strong> {{ $lead->source ?? '-' }}</p>
                <p><strong>Interest:</strong> {{ $lead->service_interest ?? '-' }}</p>
                <p><strong>Assigned:</strong> {{ $lead->assignedTo?->name ?? 'Unassigned' }}</p>
                <p><strong>Status:</strong> <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $lead->status)) }}</span></p>
                @if($lead->patient_id)
                    <p><strong>Converted:</strong> <a href="{{ route('patients.show', $lead->patient_id) }}">View Patient</a></p>
                @endif
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3"><div class="card-body">
                <h5>Update Follow-up Status</h5>
                <form method="POST" action="{{ route('leads.update-status', $lead) }}">
                    @csrf @method('PATCH')
                    <div class="form-group"><label>Status</label>
                        <select name="status" class="form-control">
                            @foreach(['new_lead','contacted','followup_1','followup_2','followup_3','followup_4','followup_5','appointment_booked','success','not_showing','reject','kiv','no_answer','wrong_number','duplicate'] as $s)
                                <option value="{{ $s }}" {{ $lead->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Follow-up Notes</label><textarea name="last_followup_notes" rows="3" class="form-control">{{ $lead->last_followup_notes }}</textarea></div>
                    <div class="form-group"><label>Next Follow-up</label><input type="datetime-local" name="next_followup_at" value="{{ $lead->next_followup_at?->format('Y-m-d\TH:i') }}" class="form-control" /></div>
                    <button class="btn btn-primary btn-sm">Update</button>
                </form>
            </div></div>
            @if(!$lead->patient_id)
            <div class="card border-success"><div class="card-body">
                <h6>Convert to Patient</h6>
                <form method="POST" action="{{ route('leads.convert', $lead) }}" onsubmit="return confirm('Convert to patient?')">
                    @csrf
                    <button class="btn btn-success btn-sm"><i class="mdi mdi-account-plus mr-1"></i>Convert Now</button>
                </form>
            </div></div>
            @endif
        </div>
    </div>
</x-app-layout>
