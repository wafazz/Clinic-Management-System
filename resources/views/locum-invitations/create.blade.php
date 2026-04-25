<x-app-layout>
    <x-slot name="header">
        <h4 class="font-weight-bold mb-0"><i class="mdi mdi-email-plus text-primary mr-2"></i>New Locum Invitation</h4>
    </x-slot>

    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('locum-invitations.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Locum Doctor <span class="text-danger">*</span></label>
                                <select name="locum_doctor_id" required class="form-control">
                                    <option value="">Select</option>
                                    @foreach($locumDoctors as $ld)
                                        <option value="{{ $ld->id }}">{{ $ld->name }} — {{ $ld->specialization ?? 'GP' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Branch <span class="text-danger">*</span></label>
                                <select name="branch_id" required class="form-control">
                                    <option value="">Select</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}" {{ session('current_branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h6 class="text-muted text-uppercase font-weight-bold mt-3 mb-2" style="font-size:0.78rem;letter-spacing:0.05em">Working Period</h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Valid From <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="valid_from" required class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" />
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Valid Until <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="valid_to" required class="form-control" value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" />
                            </div>
                        </div>

                        <h6 class="text-muted text-uppercase font-weight-bold mt-3 mb-2" style="font-size:0.78rem;letter-spacing:0.05em">Permissions</h6>
                        <div class="p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="can_consultation" value="1" id="canCons" class="form-check-input" checked>
                                <label for="canCons" class="form-check-label"><i class="mdi mdi-stethoscope text-success mr-1"></i><strong>Allow Consultations</strong> <small class="text-muted">— start consultations during the period and write clinical notes, prescriptions, MC, lab orders</small></label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="can_treatment_plan" value="1" id="canTP" class="form-check-input" checked>
                                <label for="canTP" class="form-check-label"><i class="mdi mdi-clipboard-list text-info mr-1"></i><strong>Allow Treatment Plans</strong> <small class="text-muted">— create multi-session treatment plans</small></label>
                            </div>
                            <div class="form-check mt-2 ml-4">
                                <input type="checkbox" name="treatment_plan_requires_approval" value="1" id="reqApproval" class="form-check-input" checked>
                                <label for="reqApproval" class="form-check-label"><i class="mdi mdi-shield-check text-warning mr-1"></i>Treatment plans require admin approval before activating</label>
                            </div>
                        </div>

                        <div class="form-group mt-3">
                            <label>Notes <small class="text-muted">(visible to locum)</small></label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Optional message to the locum explaining the engagement..."></textarea>
                        </div>

                        <div class="alert alert-info py-2 small mt-3">
                            <i class="mdi mdi-information-outline mr-1"></i>
                            After saving, the locum will see this invitation when they log in to <code>/locum-portal/login</code>. They must accept it before access kicks in. Access automatically ends when the period passes.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('locum-invitations.index') }}" class="btn btn-light">Cancel</a>
                            <button class="btn btn-primary"><i class="mdi mdi-email-fast mr-1"></i>Send Invitation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-2"><i class="mdi mdi-help-circle text-info mr-1"></i>How invitations work</h6>
                    <ol class="pl-3 mb-0 small text-muted" style="line-height:1.8">
                        <li>You set the period + permissions</li>
                        <li>Locum logs in to portal</li>
                        <li>They see "Pending Invitation" → accept</li>
                        <li>During the active window, "Consultations" and "Treatment Plans" appear in their portal nav</li>
                        <li>Treatment plans they create are <strong>pending</strong> until you approve</li>
                        <li>Outside the period, access is automatically gone</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
