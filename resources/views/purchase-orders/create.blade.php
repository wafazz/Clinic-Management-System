<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Purchase Order</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('purchase-orders.store') }}" x-data="poForm()">
            @csrf
            <div class="row">
                <div class="col-md-4 form-group"><label>Supplier *</label>
                    <select name="supplier_id" required class="form-control">
                        <option value="">Select</option>
                        @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group"><label>Order Date *</label><input type="date" name="order_date" required class="form-control" value="{{ now()->toDateString() }}" /></div>
                <div class="col-md-3 form-group"><label>Expected Date</label><input type="date" name="expected_date" class="form-control" /></div>
            </div>

            <h5>Items</h5>
            <template x-for="(item, idx) in items" :key="idx">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <select :name="'items['+idx+'][medicine_id]'" required class="form-control" x-on:change="setPrice($event, idx)">
                            <option value="">Select medicine</option>
                            @foreach($medicines as $m)<option value="{{ $m->id }}" data-price="{{ $m->cost_price ?? $m->unit_price ?? 0 }}">{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-2"><input type="number" :name="'items['+idx+'][quantity_ordered]'" x-model.number="item.qty" min="1" required class="form-control" placeholder="Qty" @input="calc" /></div>
                    <div class="col-md-2"><input type="number" step="0.01" :name="'items['+idx+'][cost_price]'" x-model.number="item.price" min="0" required class="form-control" placeholder="Cost" @input="calc" /></div>
                    <div class="col-md-2"><input type="text" :name="'items['+idx+'][batch_number]'" class="form-control" placeholder="Batch #" /></div>
                    <div class="col-md-1"><input type="date" :name="'items['+idx+'][expiry_date]'" class="form-control" /></div>
                    <div class="col-md-1"><button type="button" @click="remove(idx)" class="btn btn-danger btn-sm" x-show="items.length > 1">×</button></div>
                </div>
            </template>
            <button type="button" @click="add" class="btn btn-outline-primary btn-sm mb-3">+ Add Item</button>

            <div class="text-right mb-3"><h5>Total: RM <span x-text="total.toFixed(2)">0.00</span></h5></div>

            <div class="form-group"><label>Notes</label><textarea name="notes" rows="2" class="form-control"></textarea></div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create PO</button>
            </div>
        </form>
    </div></div>
    <script>
        function poForm() {
            return {
                items: [{ qty: 1, price: 0 }],
                total: 0,
                add() { this.items.push({ qty: 1, price: 0 }); },
                remove(i) { this.items.splice(i, 1); this.calc(); },
                setPrice(e, i) { this.items[i].price = parseFloat(e.target.selectedOptions[0].dataset.price) || 0; this.calc(); },
                calc() { this.total = this.items.reduce((s, x) => s + (x.qty * x.price), 0); },
            };
        }
    </script>
</x-app-layout>
