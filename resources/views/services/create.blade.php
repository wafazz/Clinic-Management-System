<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-medical-bag text-primary mr-1"></i>Add Service</h4>
                <small class="text-muted">A billable service offered at a branch</small>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Services</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="serviceForm()" x-init="init()">
        <form method="POST" action="{{ route('services.store') }}">
            @csrf
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- 1. Identity --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-tag text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Service Identity</h5>
                                <small class="text-muted">Name, branch, category</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-7 mb-2">
                                <label class="form-label small font-weight-bold">Service Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" placeholder="e.g. General Consultation, Wound Dressing, ECG" value="{{ old('name') }}" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-5 mb-2">
                                <label class="form-label small font-weight-bold">Branch *</label>
                                <select name="branch_id" required class="form-control" x-model="branchId">
                                    <option value="">&mdash; Select branch &mdash;</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', session('current_branch_id')) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <label class="form-label small font-weight-bold">Category</label>
                        <input type="hidden" name="category" :value="category">
                        <div class="d-flex flex-wrap mb-2" style="gap:6px">
                            @foreach([
                                ['Consultation','mdi-stethoscope','#3b82f6'],
                                ['Lab','mdi-flask','#8b5cf6'],
                                ['Procedure','mdi-medical-bag','#ef4444'],
                                ['Vaccination','mdi-needle','#10b981'],
                                ['Imaging','mdi-radioactive','#f59e0b'],
                                ['Wellness','mdi-heart-pulse','#ec4899'],
                                ['Other','mdi-dots-horizontal','#6b7280'],
                            ] as $cat)
                                <button type="button" @click="category = '{{ $cat[0] }}'"
                                    :style="category === '{{ $cat[0] }}' ? 'background:{{ $cat[2] }};color:#fff;border-color:{{ $cat[2] }}' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                    style="padding:8px 12px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;display:flex;align-items:center;gap:6px">
                                    <i class="mdi {{ $cat[1] }}"></i> {{ $cat[0] }}
                                </button>
                            @endforeach
                        </div>
                        <input type="text" class="form-control" placeholder="Or type a custom category" x-model="category" value="{{ old('category') }}" />

                        <div class="mt-3">
                            <label class="form-label small font-weight-bold">Description</label>
                            <textarea name="description" rows="2" class="form-control" x-model="description" placeholder="What's included? Who is it for?">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- 2. Pricing --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-cash text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Pricing</h5>
                                <small class="text-muted">What the patient pays</small>
                            </div>
                        </div>
                        <label class="form-label small font-weight-bold">Price (RM) *</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                            <input type="number" step="0.01" min="0" name="price" required class="form-control" x-model="price" value="{{ old('price', 0) }}" />
                        </div>
                        <div class="mt-2 d-flex flex-wrap" style="gap:4px">
                            @foreach([20, 35, 50, 80, 100, 150, 200, 350, 500] as $p)
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:11px" @click="price = {{ $p }}">RM {{ $p }}</button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3. Status --}}
                    <div class="data-card mb-3">
                        <input type="hidden" name="is_active" value="0" />
                        <label class="d-flex align-items-center mb-0" style="gap:12px;cursor:pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="active" checked style="display:none">
                            <span :style="active ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="active ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold" x-text="active ? 'Active' : 'Inactive'"></span>
                                <small class="d-block text-muted" x-text="active ? 'Available for billing immediately' : 'Hidden from active service lists'"></small>
                            </span>
                        </label>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-plus-circle"></i> Create Service</button>
                        <a href="{{ route('services.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div class="data-card" style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Preview
                        </small>

                        {{-- Service card --}}
                        <div class="mt-3" :style="`background:${cardGrad};color:#fff;border-radius:12px;padding:18px;position:relative;overflow:hidden`">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <span style="background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;letter-spacing:0.1em" x-text="(category || 'Service').toUpperCase()"></span>
                                <h4 class="text-white font-weight-bold mt-2 mb-1" x-text="name || 'Service Name'"></h4>
                                <div style="opacity:0.9;font-size:13px;min-height:18px" x-text="description || 'Short description here'"></div>

                                <div class="mt-3 d-flex align-items-baseline" style="gap:6px">
                                    <span style="opacity:0.85;font-size:14px">RM</span>
                                    <span style="font-size:36px;font-weight:700;line-height:1" x-text="Number(price || 0).toFixed(2)"></span>
                                </div>

                                <div class="mt-2" x-show="branchName" x-cloak>
                                    <small style="opacity:0.85;font-size:12px"><i class="mdi mdi-hospital-building"></i> <span x-text="branchName"></span></small>
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="mt-3 p-2 text-center small" :style="active ? 'background:#dcfce7;color:#166534;border:1px solid #bbf7d0' : 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5'" style="border-radius:8px">
                            <i :class="active ? 'mdi mdi-check-circle' : 'mdi mdi-close-circle'"></i>
                            <strong x-text="active ? 'Active' : 'Inactive'"></strong>
                        </div>

                        {{-- Form checklist --}}
                        <div class="mt-3">
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">Form Status</small>
                            <div class="mt-2 small">
                                <div :class="name ? 'text-success' : 'text-muted'">
                                    <i :class="name ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Service name
                                </div>
                                <div :class="branchId ? 'text-success' : 'text-muted'">
                                    <i :class="branchId ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Branch assigned
                                </div>
                                <div :class="Number(price) > 0 ? 'text-success' : 'text-muted'">
                                    <i :class="Number(price) > 0 ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Price set
                                </div>
                                <div :class="category ? 'text-success' : 'text-muted'">
                                    <i :class="category ? 'mdi mdi-check-circle' : 'mdi mdi-circle-outline'"></i> Category picked
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @php
        $branchMap = $branches->mapWithKeys(fn($b) => [$b->id => $b->name])->all();
    @endphp

    <script>
        const BRANCHES = @json($branchMap);
        const CATEGORY_GRAD = {
            'Consultation': 'linear-gradient(135deg,#1e40af,#1e3a8a)',
            'Lab':          'linear-gradient(135deg,#7c3aed,#5b21b6)',
            'Procedure':    'linear-gradient(135deg,#dc2626,#991b1b)',
            'Vaccination':  'linear-gradient(135deg,#10b981,#059669)',
            'Imaging':      'linear-gradient(135deg,#f59e0b,#d97706)',
            'Wellness':     'linear-gradient(135deg,#ec4899,#be185d)',
            'Other':        'linear-gradient(135deg,#475569,#334155)',
        };

        function serviceForm() {
            return {
                name: @json(old('name')),
                branchId: @json(old('branch_id', session('current_branch_id'))),
                category: @json(old('category')),
                description: @json(old('description')),
                price: @json(old('price', 0)),
                active: true,
                init() {},
                get branchName() { return BRANCHES[this.branchId] || ''; },
                get cardGrad() {
                    return CATEGORY_GRAD[this.category] || 'linear-gradient(135deg,#475569,#334155)';
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
