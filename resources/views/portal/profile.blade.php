@extends('portal.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h3 class="font-weight-bold mb-1"><i class="mdi mdi-account-circle text-primary mr-2"></i>My Profile</h3>
            <small class="text-muted">View your personal information and update your password.</small>
        </div>
    </div>

    <div class="row">
        {{-- Personal Info --}}
        <div class="col-lg-7 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="profile-avatar mr-3">{{ strtoupper(substr($patient->name, 0, 1)) }}</div>
                        <div>
                            <h5 class="mb-0 font-weight-bold">{{ $patient->name }}</h5>
                            <small class="text-muted">{{ $patient->patient_id }}</small>
                            @if($patient->date_of_birth)
                                <small class="text-muted">· {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} years old</small>
                            @endif
                        </div>
                    </div>

                    <h6 class="text-muted text-uppercase small font-weight-bold mb-2 mt-4" style="letter-spacing:0.05em">Personal Information</h6>
                    <dl class="detail-list mb-4">
                        <div><dt>IC Number</dt><dd><code>{{ $patient->ic_number ?? '—' }}</code></dd></div>
                        <div><dt>Gender</dt><dd>{{ ucfirst($patient->gender ?? '—') }}</dd></div>
                        <div><dt>Date of Birth</dt><dd>{{ $patient->date_of_birth?->format('d F Y') ?? '—' }}</dd></div>
                        <div><dt>Blood Type</dt><dd>{{ $patient->blood_type ?? '—' }}</dd></div>
                    </dl>

                    <h6 class="text-muted text-uppercase small font-weight-bold mb-2 mt-4" style="letter-spacing:0.05em">Contact</h6>
                    <dl class="detail-list mb-4">
                        <div><dt>Phone</dt><dd>{{ $patient->phone ?? '—' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $patient->email ?? '—' }}</dd></div>
                        <div><dt>Address</dt><dd>{{ $patient->address ?? '—' }}</dd></div>
                    </dl>

                    <h6 class="text-muted text-uppercase small font-weight-bold mb-2 mt-4" style="letter-spacing:0.05em">Medical</h6>
                    <dl class="detail-list mb-4">
                        <div><dt>Allergies</dt><dd>
                            @if($patient->allergies)
                                <span class="badge badge-danger"><i class="mdi mdi-alert"></i> {{ $patient->allergies }}</span>
                            @else
                                <span class="text-success"><i class="mdi mdi-check-circle"></i> None reported</span>
                            @endif
                        </dd></div>
                        <div><dt>Medical History</dt><dd>{{ $patient->medical_history ?? '—' }}</dd></div>
                    </dl>

                    <h6 class="text-muted text-uppercase small font-weight-bold mb-2 mt-4" style="letter-spacing:0.05em">Emergency Contact</h6>
                    <dl class="detail-list mb-0">
                        <div><dt>Name</dt><dd>{{ $patient->emergency_contact ?? '—' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $patient->emergency_phone ?? '—' }}</dd></div>
                    </dl>

                    <div class="alert alert-info mt-4 mb-0 py-2 small">
                        <i class="mdi mdi-information-outline mr-1"></i>
                        To update personal info, please contact the clinic reception.
                    </div>
                </div>
            </div>
        </div>

        {{-- Change Password --}}
        <div class="col-lg-5 mb-3">
            <div class="card" x-data="passwordForm()">
                <div class="card-body">
                    <h5 class="font-weight-bold mb-1"><i class="mdi mdi-lock text-warning mr-2"></i>Change Password</h5>
                    <p class="text-muted small mb-3">Use a strong password — at least 8 characters.</p>

                    <form method="POST" action="{{ route('portal.password') }}">
                        @csrf @method('PATCH')

                        <div class="form-group">
                            <label>Current Password</label>
                            <div class="input-group">
                                <input :type="showCurrent ? 'text' : 'password'" name="current_password" required class="form-control" autocomplete="current-password" placeholder="Enter current password" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" @click="showCurrent = !showCurrent" tabindex="-1">
                                        <i class="mdi" :class="showCurrent ? 'mdi-eye-off' : 'mdi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            @error('current_password') <small class="text-danger"><i class="mdi mdi-alert-circle"></i> {{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <div class="input-group">
                                <input :type="showNew ? 'text' : 'password'" name="password" required minlength="8" class="form-control" autocomplete="new-password"
                                       x-model="newPassword" @input="checkStrength" placeholder="Min 8 characters" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" @click="showNew = !showNew" tabindex="-1">
                                        <i class="mdi" :class="showNew ? 'mdi-eye-off' : 'mdi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            @error('password') <small class="text-danger"><i class="mdi mdi-alert-circle"></i> {{ $message }}</small> @enderror

                            {{-- Strength meter --}}
                            <div class="mt-2" x-show="newPassword.length > 0">
                                <div class="progress" style="height:5px">
                                    <div class="progress-bar" :class="strengthClass" :style="'width:' + strengthPct + '%'"></div>
                                </div>
                                <small :class="strengthTextClass" x-text="strengthLabel"></small>
                            </div>

                            {{-- Requirements checklist --}}
                            <ul class="pwd-checklist mt-2 mb-0" x-show="newPassword.length > 0">
                                <li :class="{ 'met': newPassword.length >= 8 }">
                                    <i class="mdi" :class="newPassword.length >= 8 ? 'mdi-check-circle' : 'mdi-circle-outline'"></i> At least 8 characters
                                </li>
                                <li :class="{ 'met': /[A-Z]/.test(newPassword) }">
                                    <i class="mdi" :class="/[A-Z]/.test(newPassword) ? 'mdi-check-circle' : 'mdi-circle-outline'"></i> One uppercase letter
                                </li>
                                <li :class="{ 'met': /[0-9]/.test(newPassword) }">
                                    <i class="mdi" :class="/[0-9]/.test(newPassword) ? 'mdi-check-circle' : 'mdi-circle-outline'"></i> One number
                                </li>
                                <li :class="{ 'met': /[^A-Za-z0-9]/.test(newPassword) }">
                                    <i class="mdi" :class="/[^A-Za-z0-9]/.test(newPassword) ? 'mdi-check-circle' : 'mdi-circle-outline'"></i> One special character
                                </li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <div class="input-group">
                                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required class="form-control" autocomplete="new-password"
                                       x-model="confirmPassword" placeholder="Type the new password again" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" @click="showConfirm = !showConfirm" tabindex="-1">
                                        <i class="mdi" :class="showConfirm ? 'mdi-eye-off' : 'mdi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <small x-show="confirmPassword.length > 0 && confirmPassword !== newPassword" class="text-danger">
                                <i class="mdi mdi-alert-circle"></i> Passwords don't match
                            </small>
                            <small x-show="confirmPassword.length > 0 && confirmPassword === newPassword" class="text-success">
                                <i class="mdi mdi-check-circle"></i> Passwords match
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block"
                                :disabled="newPassword.length < 8 || newPassword !== confirmPassword">
                            <i class="mdi mdi-shield-check mr-1"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- Account Security --}}
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3"><i class="mdi mdi-shield-account text-success mr-2"></i>Account Security</h6>
                    <dl class="detail-list mb-0">
                        <div><dt>Last Login</dt><dd>{{ $patient->last_portal_login?->diffForHumans() ?? 'First time' }}</dd></div>
                        <div><dt>Account ID</dt><dd><code>{{ $patient->patient_id }}</code></dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <style>
        .profile-avatar {
            width: 64px; height: 64px; border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #10b981);
            color: #fff; font-weight: 800; font-size: 1.5rem;
            display: inline-flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.3);
        }
        .pwd-checklist { list-style: none; padding: 0; font-size: 0.78rem; }
        .pwd-checklist li { color: #94a3b8; padding: 2px 0; }
        .pwd-checklist li.met { color: #10b981; }
        .pwd-checklist li i { font-size: 1em; vertical-align: middle; margin-right: 4px; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function passwordForm() {
            return {
                showCurrent: false,
                showNew: false,
                showConfirm: false,
                newPassword: '',
                confirmPassword: '',
                strengthPct: 0,
                strengthLabel: '',
                strengthClass: 'bg-danger',
                strengthTextClass: 'text-danger',
                checkStrength() {
                    let p = this.newPassword;
                    let score = 0;
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
    </script>
@endsection
