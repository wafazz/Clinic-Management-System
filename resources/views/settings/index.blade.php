<x-app-layout>
    <x-slot name="header">
        <h4 class="font-weight-bold mb-0">Settings</h4>
    </x-slot>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Clinic Branding</h5>

                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label class="form-label">Clinic Name *</label>
                            <input type="text" name="clinic_name" value="{{ old('clinic_name', $clinicName) }}" required class="form-control form-control-sm" />
                            @error('clinic_name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Clinic Logo</label>
                            @if($logo)
                                <div class="mb-3 d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $logo) }}" alt="Clinic Logo" style="max-height:60px; max-width:200px;" class="border rounded p-1" />
                                </div>
                            @endif
                            <input type="file" name="clinic_logo" accept="image/*" class="form-control form-control-sm" />
                            <small class="text-muted">Accepted: PNG, JPG, SVG, WebP. Max 2MB. Recommended: 200x50px</small>
                            @error('clinic_logo') <br><small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <hr class="my-4">
                        <h5 class="card-title"><i class="mdi mdi-whatsapp text-success mr-1"></i>WhatsApp Reminders</h5>
                        <p class="small text-muted">Configure WhatsApp API for sending appointment reminders.</p>

                        <div class="form-check mb-2">
                            <input type="checkbox" name="whatsapp_enabled" value="1" id="wa_en" class="form-check-input" {{ ($whatsapp_enabled ?? '0') === '1' ? 'checked' : '' }}>
                            <label for="wa_en" class="form-check-label">Enable WhatsApp sending</label>
                        </div>

                        <div class="form-group">
                            <label>Provider</label>
                            <select name="whatsapp_provider" class="form-control form-control-sm">
                                <option value="cloud_api" {{ ($whatsapp_provider ?? '') === 'cloud_api' ? 'selected' : '' }}>Meta WhatsApp Cloud API</option>
                                <option value="fonnte" {{ ($whatsapp_provider ?? '') === 'fonnte' ? 'selected' : '' }}>Fonnte</option>
                                <option value="wassenger" {{ ($whatsapp_provider ?? '') === 'wassenger' ? 'selected' : '' }}>Wassenger</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>API Token / Key</label>
                            <input type="password" name="whatsapp_token" value="{{ $whatsapp_token ?? '' }}" class="form-control form-control-sm" placeholder="{{ $whatsapp_token ? '••••••••' : 'Paste token here' }}" autocomplete="off" />
                        </div>

                        <div class="form-group">
                            <label>Phone Number ID <small class="text-muted">(Cloud API only)</small></label>
                            <input type="text" name="whatsapp_phone_id" value="{{ $whatsapp_phone_id ?? '' }}" class="form-control form-control-sm" placeholder="e.g. 123456789012345" />
                        </div>

                        <hr class="my-4">
                        <h5 class="card-title"><i class="mdi mdi-credit-card text-primary mr-1"></i>Billplz Payment Gateway</h5>
                        <p class="small text-muted">Accept online payments for invoices via Billplz.</p>

                        <div class="form-check mb-2">
                            <input type="checkbox" name="billplz_enabled" value="1" id="bp_en" class="form-check-input" {{ ($billplz_enabled ?? '0') === '1' ? 'checked' : '' }}>
                            <label for="bp_en" class="form-check-label">Enable Billplz</label>
                        </div>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="billplz_sandbox" value="1" id="bp_sb" class="form-check-input" {{ ($billplz_sandbox ?? '1') === '1' ? 'checked' : '' }}>
                            <label for="bp_sb" class="form-check-label">Sandbox Mode (test environment)</label>
                        </div>

                        <div class="form-group">
                            <label>API Secret Key</label>
                            <input type="password" name="billplz_api_key" value="{{ $billplz_api_key ?? '' }}" class="form-control form-control-sm" placeholder="{{ $billplz_api_key ? '••••••••' : 'From Billplz dashboard' }}" autocomplete="off" />
                        </div>

                        <div class="form-group">
                            <label>Collection ID</label>
                            <input type="text" name="billplz_collection_id" value="{{ $billplz_collection_id ?? '' }}" class="form-control form-control-sm" placeholder="e.g. abc123" />
                        </div>

                        <div class="form-group">
                            <label>X-Signature Key <small class="text-muted">(for callback verification)</small></label>
                            <input type="password" name="billplz_x_signature" value="{{ $billplz_x_signature ?? '' }}" class="form-control form-control-sm" placeholder="{{ $billplz_x_signature ? '••••••••' : 'From Billplz Settings → X-Signature' }}" autocomplete="off" />
                        </div>

                        <div class="alert alert-info py-2 small">
                            <strong>Callback URL:</strong> <code>{{ url('/billplz/callback') }}</code><br>
                            <strong>Redirect URL:</strong> <code>{{ url('/billplz/redirect') }}</code><br>
                            Add these in Billplz Collection settings.
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-content-save mr-1"></i>Save Settings
                        </button>
                    </form>

                    @if($logo)
                        <form method="POST" action="{{ route('settings.remove-logo') }}" class="mt-3" onsubmit="return confirm('Remove current logo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="mdi mdi-delete mr-1"></i>Remove Logo
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Preview</h5>
                    <div class="bg-dark rounded p-3 text-center">
                        @if($logo)
                            <img src="{{ asset('storage/' . $logo) }}" alt="Logo Preview" style="max-height:50px; max-width:180px;" />
                        @else
                            <span style="font-size:20px;font-weight:700;color:#fff;">{{ Str::limit($clinicName, 20) }}</span>
                        @endif
                    </div>
                    <small class="text-muted mt-2 d-block">This is how it appears in the sidebar header.</small>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
