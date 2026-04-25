<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="font-weight-bold mb-1"><i class="mdi mdi-pill text-primary mr-2"></i>{{ $medicine->name }}</h4>
                <small class="text-muted">{{ $medicine->generic_name ?? 'No generic name' }} · {{ $medicine->category->name ?? 'Uncategorized' }}</small>
            </div>
            <div class="d-flex" style="gap:8px">
                <a href="{{ route('medicines.index') }}" class="btn btn-light btn-sm"><i class="mdi mdi-arrow-left mr-1"></i>Back</a>
                <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-primary btn-sm"><i class="mdi mdi-pencil mr-1"></i>Edit</a>
            </div>
        </div>
    </x-slot>

    {{-- Quick stat tiles --}}
    <div class="row mb-3">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid {{ $medicine->isLowStock() ? 'var(--c-danger)' : 'var(--c-success)' }};">
                <span class="stat-pill-icon" style="background:{{ $medicine->isLowStock() ? 'rgba(239,68,68,0.12)' : 'rgba(16,185,129,0.12)' }};color:{{ $medicine->isLowStock() ? '#b91c1c' : '#047857' }};"><i class="mdi mdi-package-variant"></i></span>
                <div class="stat-pill-label">Current Stock</div>
                <div class="stat-pill-num">{{ $medicine->current_stock }}</div>
                <small class="text-muted">{{ $medicine->unit }}{{ $medicine->isLowStock() ? ' · LOW STOCK' : '' }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-warning);">
                <span class="stat-pill-icon" style="background:rgba(245,158,11,0.12);color:#b45309;"><i class="mdi mdi-alert-circle"></i></span>
                <div class="stat-pill-label">Reorder Level</div>
                <div class="stat-pill-num">{{ $medicine->reorder_level }}</div>
                <small class="text-muted">trigger threshold</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid var(--c-primary);">
                <span class="stat-pill-icon" style="background:rgba(14,165,233,0.12);color:#0369a1;"><i class="mdi mdi-currency-usd"></i></span>
                <div class="stat-pill-label">Selling Price</div>
                <div class="stat-pill-num">RM {{ number_format($medicine->selling_price, 2) }}</div>
                <small class="text-muted">cost: RM {{ number_format($medicine->cost_price, 2) }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-pill" style="border-left:4px solid {{ $medicine->isExpired() ? 'var(--c-danger)' : 'var(--c-info)' }};">
                <span class="stat-pill-icon" style="background:{{ $medicine->isExpired() ? 'rgba(239,68,68,0.12)' : 'rgba(6,182,212,0.12)' }};color:{{ $medicine->isExpired() ? '#b91c1c' : '#0e7490' }};"><i class="mdi mdi-calendar-remove"></i></span>
                <div class="stat-pill-label">Expiry Date</div>
                <div class="stat-pill-num" style="font-size:1.2rem;">{{ $medicine->expiry_date?->format('d M Y') ?? '—' }}</div>
                <small class="text-muted">{{ $medicine->isExpired() ? 'EXPIRED' : ($medicine->expiry_date ? $medicine->expiry_date->diffForHumans() : '') }}</small>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        {{-- Details --}}
        <div class="col-lg-7 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="mdi mdi-information-outline text-primary mr-2"></i>Medicine Details</h5>
                    <dl class="detail-list">
                        <div><dt>Name</dt><dd>{{ $medicine->name }}</dd></div>
                        <div><dt>Generic Name</dt><dd>{{ $medicine->generic_name ?? '—' }}</dd></div>
                        <div><dt>Category</dt><dd>{{ $medicine->category->name ?? '—' }}</dd></div>
                        <div><dt>SKU</dt><dd><code>{{ $medicine->sku ?? '—' }}</code></dd></div>
                        <div><dt>Unit</dt><dd>{{ ucfirst($medicine->unit) }}</dd></div>
                        <div><dt>Manufacturer</dt><dd>{{ $medicine->manufacturer ?? '—' }}</dd></div>
                        <div><dt>Cost Price</dt><dd>RM {{ number_format($medicine->cost_price, 2) }}</dd></div>
                        <div><dt>Selling Price</dt><dd class="text-success">RM {{ number_format($medicine->selling_price, 2) }}</dd></div>
                        <div><dt>Margin</dt><dd>
                            @php $margin = $medicine->selling_price - $medicine->cost_price; $marginPct = $medicine->cost_price > 0 ? round(($margin / $medicine->cost_price) * 100, 1) : 0; @endphp
                            <span class="text-success">RM {{ number_format($margin, 2) }}</span> <small class="text-muted">({{ $marginPct }}%)</small>
                        </dd></div>
                        <div><dt>Expiry Date</dt><dd class="{{ $medicine->isExpired() ? 'text-danger' : '' }}">{{ $medicine->expiry_date?->format('d F Y') ?? '—' }}</dd></div>
                        <div><dt>Status</dt><dd>
                            @if($medicine->is_active)
                                <span class="badge badge-success"><i class="mdi mdi-check-circle"></i> Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                            @if($medicine->isLowStock())<span class="badge badge-danger ml-1"><i class="mdi mdi-alert"></i> Low Stock</span>@endif
                            @if($medicine->isExpired())<span class="badge badge-danger ml-1"><i class="mdi mdi-clock-alert"></i> Expired</span>@endif
                        </dd></div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Adjust Stock --}}
        <div class="col-lg-5 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><i class="mdi mdi-tune text-warning mr-2"></i>Quick Stock Adjustment</h5>
                    <p class="text-muted small mb-3">Use positive numbers to add, negative to remove. Logged to stock history.</p>
                    <form method="POST" action="{{ route('medicines.adjust-stock', $medicine) }}">
                        @csrf
                        <div class="form-group">
                            <label>Type *</label>
                            <select name="type" required class="form-control">
                                <option value="purchase">📦 Purchase (Add Stock)</option>
                                <option value="adjustment">🔧 Adjustment</option>
                                <option value="return">↩️ Return (Add Back)</option>
                                <option value="expired">⏰ Expired (Remove)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Quantity *</label>
                            <input type="number" name="quantity" required class="form-control" placeholder="e.g. 100 to add, -10 to remove" />
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Reason for adjustment..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block"><i class="mdi mdi-check-bold mr-1"></i>Apply Adjustment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock movement history --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0"><i class="mdi mdi-history text-secondary mr-2"></i>Stock Movement History</h5>
                <small class="text-muted">{{ $medicine->stockMovements->count() }} entries</small>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Before</th>
                            <th class="text-right">After</th>
                            <th>By</th>
                            <th>Reference / Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicine->stockMovements->sortByDesc('created_at') as $mov)
                            @php
                                $typeColors = [
                                    'purchase' => 'success',
                                    'dispensed' => 'info',
                                    'adjustment_in' => 'success', 'adjustment_out' => 'warning',
                                    'transfer_in' => 'success', 'transfer_out' => 'warning',
                                    'expired' => 'danger', 'damaged' => 'danger',
                                    'return' => 'success', 'adjustment' => 'secondary',
                                ];
                                $color = $typeColors[$mov->type] ?? 'secondary';
                            @endphp
                            <tr>
                                <td><small>{{ $mov->created_at->format('d M Y') }}<br><span class="text-muted">{{ $mov->created_at->format('h:i A') }}</span></small></td>
                                <td><span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $mov->type)) }}</span></td>
                                <td class="text-right font-weight-bold {{ $mov->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                                </td>
                                <td class="text-right text-muted">{{ $mov->stock_before }}</td>
                                <td class="text-right font-weight-bold">{{ $mov->stock_after }}</td>
                                <td><small>{{ $mov->user->name ?? '—' }}</small></td>
                                <td><small>{{ $mov->reference ?? $mov->notes ?? '—' }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4"><i class="mdi mdi-database-off" style="font-size:32px;opacity:0.3"></i><br><small>No stock movements yet.</small></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
