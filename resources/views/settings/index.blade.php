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
