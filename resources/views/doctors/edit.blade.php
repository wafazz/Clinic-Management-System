<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-stethoscope text-primary mr-2"></i>Edit Doctor</h4>
                <small class="text-muted">Update Dr. {{ $doctor->user->name }}'s profile and credentials.</small>
            </div>
            <div class="d-flex" style="gap:8px">
                <a href="{{ route('doctors.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left mr-1"></i>Back</a>
                <a href="{{ route('doctor-schedules.index', $doctor) }}" class="btn btn-outline-info btn-sm"><i class="mdi mdi-calendar-clock mr-1"></i>Schedule</a>
            </div>
        </div>
    </x-slot>

    <div class="row" x-data="doctorForm()">
        {{-- Form (left, 8 cols) --}}
        <div class="col-lg-8 mb-3">
            <form method="POST" action="{{ route('doctors.update', $doctor) }}" id="doctorForm">
                @csrf @method('PUT')

                {{-- Account --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(14,165,233,0.12);color:#0369a1"><i class="mdi mdi-account-key"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Account</h5>
                                <small class="text-muted">Login credentials and contact</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-account"></i></span></div>
                                    <input type="text" name="name" value="{{ old('name', $doctor->user->name) }}" required class="form-control" x-model="form.name" />
                                </div>
                                @error('name') <small class="text-danger"><i class="mdi mdi-alert-circle"></i> {{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-email"></i></span></div>
                                    <input type="email" name="email" value="{{ old('email', $doctor->user->email) }}" required class="form-control" />
                                </div>
                                @error('email') <small class="text-danger"><i class="mdi mdi-alert-circle"></i> {{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Phone</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-phone"></i></span></div>
                                    <input type="text" name="phone" value="{{ old('phone', $doctor->user->phone) }}" class="form-control" placeholder="+60 12-345 6789" />
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-lock"></i></span></div>
                                    <input :type="showPwd ? 'text' : 'password'" name="password" class="form-control" autocomplete="new-password" placeholder="••••••••" x-model="newPwd" @input="checkStrength" />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" @click="showPwd = !showPwd" tabindex="-1">
                                            <i class="mdi" :class="showPwd ? 'mdi-eye-off' : 'mdi-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2" x-show="newPwd.length > 0">
                                    <div class="progress" style="height:5px">
                                        <div class="progress-bar" :class="strengthClass" :style="'width:' + strengthPct + '%'"></div>
                                    </div>
                                    <small :class="strengthTextClass" x-text="strengthLabel"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Doctor Info --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="form-section-icon" style="background:rgba(16,185,129,0.12);color:#047857"><i class="mdi mdi-stethoscope"></i></div>
                            <div class="ml-3">
                                <h5 class="mb-0 font-weight-bold">Professional Details</h5>
                                <small class="text-muted">Branch assignment, specialization, and credentials</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Branch <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-office-building"></i></span></div>
                                    <select name="branch_id" required class="form-control" x-model="form.branch">
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" data-name="{{ $branch->name }}" {{ old('branch_id', $doctor->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Specialization</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-medical-bag"></i></span></div>
                                    <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}" class="form-control" list="specOptions" placeholder="e.g. General Practice" x-model="form.specialization" />
                                </div>
                                <datalist id="specOptions">
                                    <option value="General Practice">
                                    <option value="Pediatrics">
                                    <option value="Dermatology">
                                    <option value="Cardiology">
                                    <option value="Orthopedics">
                                    <option value="ENT">
                                    <option value="Ophthalmology">
                                    <option value="Internal Medicine">
                                    <option value="Family Medicine">
                                    <option value="Obstetrics & Gynaecology">
                                </datalist>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Qualification</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-school"></i></span></div>
                                    <input type="text" name="qualification" value="{{ old('qualification', $doctor->qualification) }}" class="form-control" placeholder="e.g. MBBS, MD" x-model="form.qualification" />
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Consultation Fee</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="consultation_fee" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" class="form-control" placeholder="0.00" x-model="form.fee" />
                                </div>
                                <small class="text-muted">Auto-fills the consultation line on invoices.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>MMC Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-card-text"></i></span></div>
                                    <input type="text" name="mmc_number" value="{{ old('mmc_number', $doctor->mmc_number) }}" class="form-control text-uppercase" placeholder="MMC123456" x-model="form.mmc" />
                                </div>
                                <small class="text-muted">Malaysian Medical Council registration number.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>APC Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text"><i class="mdi mdi-certificate"></i></span></div>
                                    <input type="text" name="apc_number" value="{{ old('apc_number', $doctor->apc_number) }}" class="form-control text-uppercase" placeholder="APC2024XXXX" />
                                </div>
                                <small class="text-muted">Annual Practising Certificate number.</small>
                            </div>
                        </div>

                        {{-- Active toggle as a switch --}}
                        <div class="d-flex align-items-center justify-content-between p-3 mt-2" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
                            <div>
                                <strong>Active Doctor</strong>
                                <div class="small text-muted">Inactive doctors won't appear in appointment booking or queue assignment.</div>
                            </div>
                            <label class="toggle">
                                <input type="hidden" name="is_active" value="0" />
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $doctor->is_active) ? 'checked' : '' }} x-model="form.active" />
                                <span class="slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('doctors.index') }}" class="btn btn-light"><i class="mdi mdi-close mr-1"></i>Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="mdi mdi-content-save mr-1"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Live preview (right, 4 cols) --}}
        <div class="col-lg-4 mb-3">
            <div class="card sticky-top" style="top:80px">
                <div class="card-body">
                    <small class="text-muted text-uppercase font-weight-bold" style="letter-spacing:0.05em">Live Preview</small>
                    <div class="doctor-preview mt-3">
                        <div class="doctor-preview-avatar" x-text="initials()"></div>
                        <h5 class="mt-3 mb-1 font-weight-bold">Dr. <span x-text="form.name || 'Name'"></span></h5>
                        <div class="text-muted small mb-2">
                            <i class="mdi mdi-medical-bag"></i> <span x-text="form.specialization || 'No specialization'"></span>
                        </div>
                        <div class="badge badge-info mb-3" x-show="branchName" x-text="branchName"></div>
                        <div class="badge ml-1 mb-3" :class="form.active ? 'badge-success' : 'badge-secondary'">
                            <i class="mdi" :class="form.active ? 'mdi-check-circle' : 'mdi-pause-circle'"></i>
                            <span x-text="form.active ? 'Active' : 'Inactive'"></span>
                        </div>

                        <div class="preview-stats">
                            <div>
                                <div class="ps-label">Fee</div>
                                <div class="ps-num">RM <span x-text="parseFloat(form.fee || 0).toFixed(2)"></span></div>
                            </div>
                            <div>
                                <div class="ps-label">Qualification</div>
                                <div class="ps-num small" x-text="form.qualification || '—'" style="font-size:0.95rem"></div>
                            </div>
                        </div>

                        <hr>
                        <div class="text-left small">
                            <div class="text-muted text-uppercase mb-2" style="font-size:0.7rem;letter-spacing:0.05em">Credentials</div>
                            <div class="mb-1"><i class="mdi mdi-card-text-outline text-info"></i> MMC: <strong x-text="form.mmc || '—'"></strong></div>
                            <div><i class="mdi mdi-certificate text-warning"></i> APC: <strong>{{ $doctor->apc_number ?? '—' }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick tips --}}
            <div class="card mt-3">
                <div class="card-body py-3">
                    <h6 class="font-weight-bold mb-2"><i class="mdi mdi-lightbulb-on-outline text-warning mr-1"></i>Quick Tips</h6>
                    <ul class="pl-3 mb-0 small text-muted" style="line-height:1.8">
                        <li>Setting <strong>Inactive</strong> hides this doctor from booking but keeps history.</li>
                        <li>The <strong>consultation fee</strong> auto-prefills invoices when this doctor sees a patient.</li>
                        <li>Leave password blank to keep the existing one.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-section-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .input-group-text {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #64748b;
        }

        /* Toggle switch */
        .toggle { position: relative; display: inline-block; width: 56px; height: 30px; cursor: pointer; }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle .slider {
            position: absolute; inset: 0;
            background: #cbd5e1;
            border-radius: 999px;
            transition: 0.3s;
        }
        .toggle .slider::before {
            content: ''; position: absolute;
            width: 24px; height: 24px;
            left: 3px; top: 3px;
            background: #fff; border-radius: 50%;
            transition: 0.3s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .toggle input:checked + .slider {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .toggle input:checked + .slider::before {
            transform: translateX(26px);
        }

        /* Live preview */
        .doctor-preview {
            text-align: center;
            padding: 12px 8px;
        }
        .doctor-preview-avatar {
            width: 84px; height: 84px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: #fff;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 800;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        }
        .preview-stats {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 8px; margin-top: 16px;
        }
        .preview-stats > div {
            background: #f8fafc; border-radius: 10px; padding: 10px 12px;
            text-align: left; border: 1px solid #e2e8f0;
        }
        .ps-label { font-size: 0.65rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        .ps-num { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-top: 2px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function doctorForm() {
            return {
                form: {
                    name: @json($doctor->user->name),
                    branch: @json((string) $doctor->branch_id),
                    specialization: @json($doctor->specialization ?? ''),
                    qualification: @json($doctor->qualification ?? ''),
                    fee: @json($doctor->consultation_fee ?? 0),
                    mmc: @json($doctor->mmc_number ?? ''),
                    active: {{ $doctor->is_active ? 'true' : 'false' }},
                },
                showPwd: false,
                newPwd: '',
                strengthPct: 0,
                strengthLabel: '',
                strengthClass: 'bg-danger',
                strengthTextClass: 'text-danger',
                get branchName() {
                    const branches = @json($branches->mapWithKeys(fn($b) => [(string)$b->id => $b->name]));
                    return branches[this.form.branch] || '';
                },
                initials() {
                    return (this.form.name || '?').trim().split(/\s+/).slice(0, 2).map(s => s[0]?.toUpperCase()).join('') || '?';
                },
                checkStrength() {
                    let p = this.newPwd, score = 0;
                    if (p.length >= 8) score++;
                    if (p.length >= 12) score++;
                    if (/[A-Z]/.test(p)) score++;
                    if (/[0-9]/.test(p)) score++;
                    if (/[^A-Za-z0-9]/.test(p)) score++;
                    this.strengthPct = (score / 5) * 100;
                    if (score <= 1) { this.strengthLabel = 'Very weak'; this.strengthClass = 'bg-danger'; this.strengthTextClass = 'text-danger'; }
                    else if (score === 2) { this.strengthLabel = 'Weak'; this.strengthClass = 'bg-warning'; this.strengthTextClass = 'text-warning'; }
                    else if (score === 3) { this.strengthLabel = 'Fair'; this.strengthClass = 'bg-info'; this.strengthTextClass = 'text-info'; }
                    else if (score === 4) { this.strengthLabel = 'Strong'; this.strengthClass = 'bg-primary'; this.strengthTextClass = 'text-primary'; }
                    else { this.strengthLabel = 'Excellent'; this.strengthClass = 'bg-success'; this.strengthTextClass = 'text-success'; }
                }
            }
        }

        // Show saving state on submit
        document.getElementById('doctorForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving...';
        });
    </script>
</x-app-layout>
