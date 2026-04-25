<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Stock Transfer</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('stock-transfers.store') }}" x-data="{ items: [{}] }">
            @csrf
            <div class="form-group"><label>Transfer To Branch *</label>
                <select name="to_branch_id" required class="form-control">
                    <option value="">Select destination</option>
                    @foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
            </div>
            <h5>Items</h5>
            <template x-for="(it, idx) in items" :key="idx">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <select :name="'items['+idx+'][medicine_id]'" required class="form-control">
                            <option value="">Select medicine</option>
                            @foreach($medicines as $m)<option value="{{ $m->id }}">{{ $m->name }} (Stock: {{ $m->current_stock }})</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3"><input type="number" :name="'items['+idx+'][quantity]'" min="1" required class="form-control" placeholder="Quantity" /></div>
                    <div class="col-md-2"><input type="text" :name="'items['+idx+'][batch_number]'" class="form-control" placeholder="Batch" /></div>
                    <div class="col-md-1"><button type="button" @click="items.splice(idx, 1)" class="btn btn-danger btn-sm" x-show="items.length > 1">×</button></div>
                </div>
            </template>
            <button type="button" @click="items.push({})" class="btn btn-outline-primary btn-sm mb-3">+ Add Item</button>
            <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('stock-transfers.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Request Transfer</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
