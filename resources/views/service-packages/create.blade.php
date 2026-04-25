<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-package-variant text-primary mr-1"></i>New Service Package</h4>
                <small class="text-muted">Bundle services into a sellable package</small>
            </div>
            <a href="{{ route('service-packages.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Packages</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="pkgForm()" x-init="init()">
        <form method="POST" action="{{ route('service-packages.store') }}">
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
                                <h5 class="mb-0 font-weight-bold">1. Package Identity</h5>
                                <small class="text-muted">Name, type, billing cycle</small>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Package Name *</label>
                            <input type="text" name="name" required class="form-control" x-model="name" placeholder="e.g. Mom &amp; Baby Wellness Bundle" value="{{ old('name') }}" />
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Description</label>
                            <textarea name="description" rows="2" class="form-control" x-model="description" placeholder="What does this package include? Who is it for?">{{ old('description') }}</textarea>
                        </div>

                        <label class="form-label small font-weight-bold">Type *</label>
                        <input type="hidden" name="type" :value="type">
                        <div class="d-flex flex-wrap mb-3" style="gap:6px">
                            @foreach([['one_time','One-Time','mdi-cart-outline','#3b82f6','Pay once, use once'],['subscription','Subscription','mdi-autorenew','#10b981','Recurring billing'],['bundle','Bundle','mdi-package-variant','#f59e0b','Multi-item package']] as $t)
                                <button type="button" @click="type = '{{ $t[0] }}'"
                                    :style="type === '{{ $t[0] }}' ? 'background:{{ $t[3] }};color:#fff;border-color:{{ $t[3] }}' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                    style="padding:10px 14px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:140px;text-align:left">
                                    <div><i class="mdi {{ $t[2] }}"></i> {{ $t[1] }}</div>
                                    <small style="opacity:0.85;display:block;font-size:10px;font-weight:500">{{ $t[4] }}</small>
                                </button>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Price (RM) *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="price" required class="form-control" x-model="price" value="{{ old('price') }}" />
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Billing Cycle</label>
                                <select name="billing_cycle" class="form-control" x-model="cycle">
                                    <option value="once">One-time</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Duration (days)</label>
                                <input type="number" min="1" name="duration_days" class="form-control" x-model="duration" placeholder="e.g. 365" value="{{ old('duration_days') }}" />
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold">Max Visits</label>
                                <input type="number" min="1" name="max_visits" class="form-control" x-model="maxVisits" placeholder="Leave blank for unlimited" value="{{ old('max_visits') }}" />
                            </div>
                        </div>
                    </div>

                    {{-- 2. Items --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap" style="gap:10px">
                            <div class="d-flex align-items-center" style="gap:10px">
                                <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                    <i class="mdi mdi-format-list-bulleted text-white" style="font-size:18px"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 font-weight-bold">2. Package Items</h5>
                                    <small class="text-muted">What's inside?</small>
                                </div>
                            </div>
                            <span class="badge badge-primary" x-text="items.length + ' items'"></span>
                        </div>

                        <template x-for="(it, idx) in items" :key="idx">
                            <div class="p-2 mb-2" style="background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                                <div class="d-flex align-items-center mb-2" style="gap:8px">
                                    <span class="font-weight-bold" x-text="'#' + (idx + 1)" style="color:#6b7280;font-size:12px"></span>
                                    <span class="badge" :class="typeBadge(it.item_type)" x-text="typeLabel(it.item_type)"></span>
                                    <button type="button" @click="items.splice(idx, 1)" x-show="items.length > 1" class="btn btn-sm btn-outline-danger ml-auto py-0 px-2"><i class="mdi mdi-close"></i></button>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="small text-muted">Type</label>
                                        <select :name="'items['+idx+'][item_type]'" x-model="it.item_type" class="form-control form-control-sm">
                                            <option value="consultation">Consultation</option>
                                            <option value="lab">Lab</option>
                                            <option value="medicine">Medicine</option>
                                            <option value="service">Service</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 mb-2">
                                        <label class="small text-muted">Description *</label>
                                        <input type="text" :name="'items['+idx+'][description]'" required x-model="it.description" class="form-control form-control-sm" placeholder="e.g. Pre-natal consultation" />
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted">Qty *</label>
                                        <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="it.quantity" min="1" required class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted">Unit RM</label>
                                        <input type="number" step="0.01" :name="'items['+idx+'][unit_value]'" x-model.number="it.unit_value" class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="d-flex flex-wrap" style="gap:6px">
                            <button type="button" @click="addItem('consultation')" class="btn btn-sm btn-outline-info"><i class="mdi mdi-stethoscope"></i> + Consultation</button>
                            <button type="button" @click="addItem('lab')" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-flask"></i> + Lab</button>
                            <button type="button" @click="addItem('medicine')" class="btn btn-sm btn-outline-success"><i class="mdi mdi-pill"></i> + Medicine</button>
                            <button type="button" @click="addItem('service')" class="btn btn-sm btn-outline-warning"><i class="mdi mdi-medical-bag"></i> + Service</button>
                        </div>
                    </div>

                    {{-- 3. Partial Payment --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-cash-multiple text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Payment Options</h5>
                            </div>
                        </div>

                        <input type="hidden" name="allow_partial_payment" value="0" />
                        <label class="d-flex align-items-center mb-0 p-3" style="gap:12px;cursor:pointer;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                            <input type="checkbox" name="allow_partial_payment" value="1" x-model="allowPartial" style="display:none">
                            <span :style="allowPartial ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="allowPartial ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold"><i class="mdi mdi-credit-card-wireless text-info"></i> Allow Partial Payment</span>
                                <small class="d-block text-muted">Let patients pay a deposit and complete later</small>
                            </span>
                        </label>

                        <div x-show="allowPartial" x-cloak class="row mt-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Min Deposit (RM)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="min_deposit_amount" class="form-control" x-model="depositAmt" placeholder="e.g. 100" />
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Min Deposit (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="min_deposit_percent" class="form-control" x-model="depositPct" placeholder="e.g. 30" />
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <small class="text-muted" x-show="depositPct && price > 0" x-cloak>= RM <span x-text="(price * depositPct / 100).toFixed(2)"></span></small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-plus-circle"></i> Create Package</button>
                        <a href="{{ route('service-packages.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live preview --}}
                <div class="col-lg-4">
                    <div style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Package Preview
                        </small>

                        {{-- Package card --}}
                        <div class="mt-3" :style="`background:${cardGrad};color:#fff;border-radius:14px;padding:20px;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(0,0,0,0.12)`">
                            <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <span style="background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:6px;font-size:10px;font-weight:700;letter-spacing:0.1em" x-text="typeLabel(type).toUpperCase()"></span>
                                <h3 class="text-white font-weight-bold mt-2 mb-1" x-text="name || 'Package Name'"></h3>
                                <div style="opacity:0.9;font-size:13px;min-height:18px" x-text="description || 'Short pitch goes here'"></div>

                                <div class="mt-3 d-flex align-items-baseline" style="gap:6px">
                                    <span style="opacity:0.85;font-size:14px">RM</span>
                                    <span style="font-size:36px;font-weight:700;line-height:1" x-text="Number(price || 0).toFixed(0)"></span>
                                    <span style="opacity:0.85;font-size:13px" x-text="cycle === 'once' ? '' : '/ ' + cycle.replace('ly', '')"></span>
                                </div>

                                <div class="mt-2 d-flex flex-wrap" style="gap:6px;font-size:11px">
                                    <span x-show="duration" x-cloak style="background:rgba(255,255,255,0.2);padding:3px 8px;border-radius:4px"><i class="mdi mdi-calendar-range"></i> <span x-text="duration"></span> days</span>
                                    <span x-show="maxVisits" x-cloak style="background:rgba(255,255,255,0.2);padding:3px 8px;border-radius:4px"><i class="mdi mdi-check-all"></i> <span x-text="maxVisits"></span> visits</span>
                                    <span x-show="!maxVisits" x-cloak style="background:rgba(255,255,255,0.2);padding:3px 8px;border-radius:4px"><i class="mdi mdi-infinity"></i> Unlimited</span>
                                </div>
                            </div>
                        </div>

                        {{-- Items list --}}
                        <div class="mt-3 p-3" style="background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb">
                            <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                                <i class="mdi mdi-format-list-bulleted"></i> What's Included
                            </small>
                            <div class="mt-2">
                                <template x-for="(it, idx) in items" :key="idx">
                                    <div class="d-flex align-items-center mb-2 small" style="gap:8px" x-show="it.description">
                                        <span class="badge" :class="typeBadge(it.item_type)" style="font-size:9px" x-text="typeLabel(it.item_type)"></span>
                                        <span style="flex:1" x-text="it.description"></span>
                                        <strong x-text="'×' + it.quantity"></strong>
                                    </div>
                                </template>
                                <div x-show="!items.some(i => i.description)" x-cloak class="text-muted small" style="font-style:italic">
                                    Add items to see them here
                                </div>
                            </div>
                        </div>

                        {{-- Value breakdown --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="totalValue > 0" x-cloak>
                            <small style="color:#92400e;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-calculator"></i> Item Value vs Price
                            </small>
                            <div class="d-flex justify-content-between mt-2 small">
                                <span class="text-muted">Total item value</span>
                                <strong style="color:#78350f">RM <span x-text="totalValue.toFixed(2)"></span></strong>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">Package price</span>
                                <strong style="color:#78350f">RM <span x-text="Number(price || 0).toFixed(2)"></span></strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between font-weight-bold" :style="savings >= 0 ? 'color:#16a34a' : 'color:#dc2626'">
                                <span x-text="savings >= 0 ? 'Patient saves' : 'Markup'"></span>
                                <span>RM <span x-text="Math.abs(savings).toFixed(2)"></span> <small x-text="'(' + savingsPct + '%)'"></small></span>
                            </div>
                        </div>

                        {{-- Partial payment hint --}}
                        <div class="mt-3 p-2 small" x-show="allowPartial && (depositAmt || depositPct)" x-cloak style="background:#dbeafe;color:#1e3a8a;border:1px solid #93c5fd;border-radius:6px">
                            <i class="mdi mdi-credit-card-wireless"></i>
                            <strong>Partial OK:</strong> min RM <span x-text="effectiveDeposit"></span> deposit
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const TYPE_BADGES = {
            consultation: 'badge-info',
            lab: 'badge-primary',
            medicine: 'badge-success',
            service: 'badge-warning',
        };
        const TYPE_LABELS = {
            consultation: 'Consult',
            lab: 'Lab',
            medicine: 'Medicine',
            service: 'Service',
            one_time: 'One-Time',
            subscription: 'Subscription',
            bundle: 'Bundle',
        };

        function pkgForm() {
            return {
                name: @json(old('name')),
                description: @json(old('description')),
                type: @json(old('type', 'one_time')),
                price: @json(old('price', 0)),
                cycle: @json(old('billing_cycle', 'once')),
                duration: @json(old('duration_days')),
                maxVisits: @json(old('max_visits')),
                allowPartial: false,
                depositAmt: '',
                depositPct: '',
                items: [{ item_type: 'consultation', description: '', quantity: 1, unit_value: 0 }],
                init() {},
                addItem(type) {
                    this.items.push({ item_type: type, description: '', quantity: 1, unit_value: 0 });
                },
                typeBadge(t) { return TYPE_BADGES[t] || 'badge-secondary'; },
                typeLabel(t) { return TYPE_LABELS[t] || t; },
                get cardGrad() {
                    if (this.type === 'subscription') return 'linear-gradient(135deg,#10b981,#059669)';
                    if (this.type === 'bundle') return 'linear-gradient(135deg,#f59e0b,#d97706)';
                    const p = Number(this.price || 0);
                    if (p >= 1000) return 'linear-gradient(135deg,#7c3aed,#5b21b6)';
                    return 'linear-gradient(135deg,#1e40af,#1e3a8a)';
                },
                get totalValue() {
                    return this.items.reduce((sum, it) => sum + (Number(it.quantity || 0) * Number(it.unit_value || 0)), 0);
                },
                get savings() {
                    return this.totalValue - Number(this.price || 0);
                },
                get savingsPct() {
                    if (!this.totalValue) return '0';
                    return Math.round((this.savings / this.totalValue) * 100);
                },
                get effectiveDeposit() {
                    const amt = Number(this.depositAmt || 0);
                    const pct = Number(this.depositPct || 0);
                    const fromPct = (Number(this.price || 0) * pct) / 100;
                    return Math.max(amt, fromPct).toFixed(2);
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
