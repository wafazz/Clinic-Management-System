<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Stock Adjustment</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('stock-adjustments.store') }}" x-data="{ items: [{}] }">
            @csrf
            <div class="row">
                <div class="col-md-4 form-group"><label>Type *</label>
                    <select name="type" required class="form-control">
                        <option value="adjustment_in">Adjustment In (Add Stock)</option>
                        <option value="adjustment_out">Adjustment Out (Remove Stock)</option>
                        <option value="expired">Expired</option>
                        <option value="damaged">Damaged</option>
                    </select>
                </div>
                <div class="col-md-8 form-group"><label>Reason *</label><input type="text" name="reason" required class="form-control" placeholder="Reason for adjustment..." /></div>
            </div>

            <h5>Items</h5>
            <template x-for="(it, idx) in items" :key="idx">
                <div class="row mb-2">
                    <div class="col-md-5">
                        <select :name="'items['+idx+'][medicine_id]'" required class="form-control">
                            <option value="">Select medicine</option>
                            @foreach($medicines as $m)<option value="{{ $m->id }}">{{ $m->name }} (Stock: {{ $m->current_stock }})</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="number" :name="'items['+idx+'][quantity]'" min="1" required class="form-control" placeholder="Qty" /></div>
                    <div class="col-md-2"><input type="text" :name="'items['+idx+'][batch_number]'" class="form-control" placeholder="Batch" /></div>
                    <div class="col-md-2"><input type="text" :name="'items['+idx+'][notes]'" class="form-control" placeholder="Notes" /></div>
                    <div class="col-md-1"><button type="button" @click="items.splice(idx, 1)" class="btn btn-danger btn-sm" x-show="items.length > 1">×</button></div>
                </div>
            </template>
            <button type="button" @click="items.push({})" class="btn btn-outline-primary btn-sm mb-3">+ Add Item</button>

            <div class="d-flex justify-content-end">
                <a href="{{ route('stock-adjustments.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Submit Adjustment</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
