<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:10px">
            <div>
                <h4 class="font-weight-bold mb-0"><i class="mdi mdi-crown text-warning mr-1"></i>New Membership Tier</h4>
                <small class="text-muted">Build a pricing plan for your patients</small>
            </div>
            <a href="{{ route('membership-tiers.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left"></i> Back to Tiers</a>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="tierForm()" x-init="init()">
        <form method="POST" action="{{ route('membership-tiers.store') }}">
            @csrf
            <div class="row">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- Quick templates --}}
                    <div class="data-card mb-3" style="background:#f0f9ff;border:1px solid #bae6fd">
                        <small style="color:#075985;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                            <i class="mdi mdi-flash"></i> Start From a Template
                        </small>
                        <div class="mt-2 d-flex flex-wrap" style="gap:6px">
                            <button type="button" class="btn btn-sm btn-outline-secondary" @click="applyTemplate('free')"><i class="mdi mdi-gift-outline"></i> Free</button>
                            <button type="button" class="btn btn-sm btn-outline-info" @click="applyTemplate('basic')"><i class="mdi mdi-card-bulleted"></i> Basic</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" @click="applyTemplate('plus')"><i class="mdi mdi-star"></i> Plus</button>
                            <button type="button" class="btn btn-sm btn-outline-warning" @click="applyTemplate('premium')"><i class="mdi mdi-crown"></i> Premium</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" @click="applyTemplate('family')"><i class="mdi mdi-account-group"></i> Family</button>
                        </div>
                    </div>

                    {{-- 1. Identity --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#2563eb);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-tag text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">1. Tier Identity</h5>
                                <small class="text-muted">Name, price, billing cycle</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label small font-weight-bold">Tier Name *</label>
                                <input type="text" name="name" required class="form-control" x-model="name" placeholder="e.g. Premium, Family Plus, Wellness Pro" value="{{ old('name') }}" />
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label small font-weight-bold">Description</label>
                                <textarea name="description" rows="2" class="form-control" x-model="description" placeholder="One-line pitch &mdash; what makes this tier valuable?">{{ old('description') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Billing Cycle *</label>
                                <div class="d-flex flex-wrap" style="gap:6px">
                                    @foreach([['free','Free','mdi-gift'],['monthly','Monthly','mdi-calendar-month'],['yearly','Yearly','mdi-calendar-star']] as $c)
                                        <button type="button" @click="cycle = '{{ $c[0] }}'; if (cycle === 'free') price = 0;"
                                            :style="cycle === '{{ $c[0] }}' ? 'background:#1e40af;color:#fff;border-color:#1e40af' : 'background:#fff;color:#374151;border-color:#d1d5db'"
                                            style="padding:8px 14px;border-radius:8px;border:2px solid;font-weight:600;font-size:13px;transition:all 0.15s;flex:1;min-width:90px">
                                            <i class="mdi {{ $c[2] }}"></i> {{ $c[1] }}
                                        </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="billing_cycle" :value="cycle">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small font-weight-bold">Price (RM) *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">RM</span></div>
                                    <input type="number" step="0.01" min="0" name="price" required class="form-control" x-model="price" :disabled="cycle === 'free'" value="{{ old('price', 0) }}" />
                                </div>
                                <small class="text-muted" x-text="cycle === 'free' ? 'Free tier — no charge' : (cycle === 'monthly' ? 'Per month' : 'Per year')"></small>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Discounts --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-percent text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">2. Discounts</h5>
                                <small class="text-muted">% off when paying for these services</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small font-weight-bold"><i class="mdi mdi-stethoscope" style="color:#3b82f6"></i> Consultation</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="discount_consultation" class="form-control" x-model="discount.consultation" value="{{ old('discount_consultation', 0) }}" />
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <input type="range" min="0" max="100" step="5" class="form-control-range mt-1" x-model="discount.consultation">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small font-weight-bold"><i class="mdi mdi-pill" style="color:#10b981"></i> Medicine</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="discount_medicine" class="form-control" x-model="discount.medicine" value="{{ old('discount_medicine', 0) }}" />
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <input type="range" min="0" max="100" step="5" class="form-control-range mt-1" x-model="discount.medicine">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small font-weight-bold"><i class="mdi mdi-flask" style="color:#8b5cf6"></i> Lab Tests</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" max="100" name="discount_lab" class="form-control" x-model="discount.lab" value="{{ old('discount_lab', 0) }}" />
                                    <div class="input-group-append"><span class="input-group-text">%</span></div>
                                </div>
                                <input type="range" min="0" max="100" step="5" class="form-control-range mt-1" x-model="discount.lab">
                            </div>
                        </div>
                    </div>

                    {{-- 3. Free quotas --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-gift text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">3. Annual Free Allowance</h5>
                                <small class="text-muted">How many free items per year</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold"><i class="mdi mdi-stethoscope"></i> Free Consultations</label>
                                <input type="number" min="0" name="free_consultations_per_year" class="form-control" x-model="freeConsultations" value="{{ old('free_consultations_per_year', 0) }}" />
                                <small class="text-muted">per year</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold"><i class="mdi mdi-flask"></i> Free Lab Tests</label>
                                <input type="number" min="0" name="free_lab_tests_per_year" class="form-control" x-model="freeLabs" value="{{ old('free_lab_tests_per_year', 0) }}" />
                                <small class="text-muted">per year</small>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small font-weight-bold"><i class="mdi mdi-account-group"></i> Max Family Members</label>
                                <input type="number" min="0" name="max_family_members" class="form-control" x-model="maxFamily" value="{{ old('max_family_members', 0) }}" />
                                <small class="text-muted">0 = individual only</small>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Perks --}}
                    <div class="data-card mb-3">
                        <div class="d-flex align-items-center mb-3" style="gap:10px">
                            <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);display:flex;align-items:center;justify-content:center">
                                <i class="mdi mdi-star text-white" style="font-size:18px"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold">4. Perks</h5>
                            </div>
                        </div>
                        <input type="hidden" name="priority_queue" value="0" />
                        <label class="d-flex align-items-center mb-0 p-3" style="gap:12px;cursor:pointer;background:#f8fafc;border-radius:8px;border:1px solid #e5e7eb">
                            <input type="checkbox" name="priority_queue" value="1" x-model="priorityQueue" {{ old('priority_queue') ? 'checked' : '' }} style="display:none">
                            <span :style="priorityQueue ? 'background:#10b981' : 'background:#d1d5db'"
                                style="width:44px;height:24px;border-radius:12px;position:relative;transition:background 0.15s;flex-shrink:0">
                                <span :style="priorityQueue ? 'transform:translateX(20px)' : 'transform:translateX(0)'"
                                    style="position:absolute;top:2px;left:2px;width:20px;height:20px;background:#fff;border-radius:50%;transition:transform 0.15s;box-shadow:0 1px 3px rgba(0,0,0,0.2)"></span>
                            </span>
                            <span>
                                <span class="font-weight-bold"><i class="mdi mdi-rocket-launch text-warning"></i> Priority Queue</span>
                                <small class="d-block text-muted" x-text="priorityQueue ? 'Members skip ahead in walk-in queue' : 'Members wait their turn like everyone'"></small>
                            </span>
                        </label>
                    </div>

                    <div class="d-flex" style="gap:8px">
                        <button type="submit" class="btn btn-primary font-weight-bold"><i class="mdi mdi-plus-circle"></i> Create Tier</button>
                        <a href="{{ route('membership-tiers.index') }}" class="btn btn-light">Cancel</a>
                    </div>
                </div>

                {{-- RIGHT: live pricing card preview --}}
                <div class="col-lg-4">
                    <div style="position:sticky;top:80px">
                        <small class="text-muted font-weight-bold" style="text-transform:uppercase;letter-spacing:0.05em">
                            <i class="mdi mdi-eye"></i> Live Pricing Card
                        </small>

                        <div class="mt-3" :style="`background:${cardGrad};color:#fff;border-radius:14px;padding:24px;position:relative;overflow:hidden;box-shadow:0 12px 32px rgba(0,0,0,0.12)`">
                            <div style="position:absolute;top:-30px;right:-30px;width:160px;height:160px;background:rgba(255,255,255,0.06);border-radius:50%"></div>
                            <div style="position:relative">
                                <div style="font-size:11px;letter-spacing:0.15em;font-weight:700;opacity:0.85;text-transform:uppercase">
                                    <i class="mdi" :class="tierIcon"></i> Tier
                                </div>
                                <h2 class="text-white font-weight-bold mt-1 mb-2" x-text="name || 'Tier Name'"></h2>
                                <div class="mb-2" style="opacity:0.9;font-size:13px;min-height:18px" x-text="description || 'A short pitch goes here'"></div>

                                <div class="mt-3 mb-3 d-flex align-items-baseline" style="gap:6px">
                                    <span x-show="cycle !== 'free'" x-cloak style="font-size:18px;opacity:0.85">RM</span>
                                    <span style="font-size:42px;font-weight:700;line-height:1" x-text="cycle === 'free' ? 'FREE' : Number(price || 0).toFixed(0)"></span>
                                    <span x-show="cycle !== 'free'" x-cloak style="opacity:0.85;font-size:13px" x-text="cycle === 'monthly' ? '/month' : '/year'"></span>
                                </div>

                                <div style="border-top:1px solid rgba(255,255,255,0.2);padding-top:14px">
                                    <div class="small" style="opacity:0.95">
                                        <template x-for="benefit in benefits" :key="benefit">
                                            <div class="d-flex align-items-start mb-2" style="gap:8px">
                                                <i class="mdi mdi-check-circle" style="color:#86efac;flex-shrink:0;font-size:16px"></i>
                                                <span x-text="benefit"></span>
                                            </div>
                                        </template>
                                        <div x-show="!benefits.length" x-cloak class="small" style="opacity:0.7;font-style:italic">
                                            Add discounts or free items to see benefits here
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Annual value calc --}}
                        <div class="mt-3 p-3" style="background:#fffbeb;border-radius:10px;border:1px solid #fde68a" x-show="annualValue > 0" x-cloak>
                            <small style="color:#92400e;letter-spacing:0.05em;text-transform:uppercase;font-weight:700">
                                <i class="mdi mdi-calculator"></i> Estimated Annual Value
                            </small>
                            <div class="font-weight-bold mt-1" style="color:#78350f;font-size:20px">~RM <span x-text="annualValue.toFixed(0)"></span></div>
                            <small class="text-muted">Based on free quotas (RM 60 consultation, RM 80 lab)</small>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const TEMPLATES = {
            free:    { name:'Free', description:'Basic access, no charge', cycle:'free', price:0, dc:0, dm:0, dl:0, fc:1, fl:0, fam:0, pq:false },
            basic:   { name:'Basic', description:'Light savings on daily care', cycle:'monthly', price:29, dc:5, dm:5, dl:0, fc:2, fl:0, fam:0, pq:false },
            plus:    { name:'Plus', description:'Better discounts and more free visits', cycle:'monthly', price:59, dc:10, dm:10, dl:10, fc:4, fl:1, fam:0, pq:true },
            premium: { name:'Premium', description:'Top-tier care with priority access', cycle:'yearly', price:799, dc:20, dm:15, dl:20, fc:12, fl:4, fam:0, pq:true },
            family:  { name:'Family', description:'Cover up to 5 family members', cycle:'yearly', price:999, dc:15, dm:10, dl:15, fc:8, fl:2, fam:5, pq:true },
        };

        function tierForm() {
            return {
                name: @json(old('name')),
                description: @json(old('description')),
                cycle: @json(old('billing_cycle', 'monthly')),
                price: @json(old('price', 0)),
                discount: {
                    consultation: @json(old('discount_consultation', 0)),
                    medicine: @json(old('discount_medicine', 0)),
                    lab: @json(old('discount_lab', 0)),
                },
                freeConsultations: @json(old('free_consultations_per_year', 0)),
                freeLabs: @json(old('free_lab_tests_per_year', 0)),
                maxFamily: @json(old('max_family_members', 0)),
                priorityQueue: {{ old('priority_queue') ? 'true' : 'false' }},
                init() {},
                applyTemplate(key) {
                    const t = TEMPLATES[key];
                    if (!t) return;
                    if (this.name && !confirm('Replace current values with the "' + t.name + '" template?')) return;
                    this.name = t.name;
                    this.description = t.description;
                    this.cycle = t.cycle;
                    this.price = t.price;
                    this.discount.consultation = t.dc;
                    this.discount.medicine = t.dm;
                    this.discount.lab = t.dl;
                    this.freeConsultations = t.fc;
                    this.freeLabs = t.fl;
                    this.maxFamily = t.fam;
                    this.priorityQueue = t.pq;
                },
                get tierIcon() {
                    if (this.cycle === 'free') return 'mdi-gift-outline';
                    if (Number(this.price) >= 500) return 'mdi-crown';
                    if (Number(this.price) >= 100) return 'mdi-star';
                    return 'mdi-card-bulleted';
                },
                get cardGrad() {
                    if (this.cycle === 'free') return 'linear-gradient(135deg,#475569,#334155)';
                    const p = Number(this.price);
                    if (p >= 500) return 'linear-gradient(135deg,#f59e0b,#d97706)';
                    if (p >= 100) return 'linear-gradient(135deg,#7c3aed,#5b21b6)';
                    if (p >= 50) return 'linear-gradient(135deg,#1e40af,#1e3a8a)';
                    return 'linear-gradient(135deg,#0e7490,#155e75)';
                },
                get benefits() {
                    const out = [];
                    if (Number(this.discount.consultation) > 0) out.push(`${this.discount.consultation}% off consultations`);
                    if (Number(this.discount.medicine) > 0) out.push(`${this.discount.medicine}% off medicine`);
                    if (Number(this.discount.lab) > 0) out.push(`${this.discount.lab}% off lab tests`);
                    if (Number(this.freeConsultations) > 0) out.push(`${this.freeConsultations} free consultations / year`);
                    if (Number(this.freeLabs) > 0) out.push(`${this.freeLabs} free lab tests / year`);
                    if (Number(this.maxFamily) > 0) out.push(`Covers up to ${this.maxFamily} family members`);
                    if (this.priorityQueue) out.push('Priority queue access');
                    return out;
                },
                get annualValue() {
                    return Number(this.freeConsultations || 0) * 60 + Number(this.freeLabs || 0) * 80;
                },
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .data-card { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px; }
    </style>
</x-app-layout>
