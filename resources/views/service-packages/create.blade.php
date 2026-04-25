<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Service Package</h4></x-slot>
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('service-packages.store') }}" x-data="{ items: [{ item_type: 'consultation', description: '', quantity: 1, unit_value: 0 }], allowPartial: false }">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group"><label>Name *</label><input type="text" name="name" required class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Type *</label>
                    <select name="type" class="form-control"><option value="one_time">One-Time</option><option value="subscription">Subscription</option><option value="bundle">Bundle</option></select>
                </div>
                <div class="col-md-3 form-group"><label>Price *</label><input type="number" step="0.01" name="price" required class="form-control" /></div>
            </div>
            <div class="row">
                <div class="col-md-3 form-group"><label>Billing Cycle</label>
                    <select name="billing_cycle" class="form-control"><option value="once">Once</option><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="yearly">Yearly</option></select>
                </div>
                <div class="col-md-3 form-group"><label>Duration (days)</label><input type="number" name="duration_days" class="form-control" /></div>
                <div class="col-md-3 form-group"><label>Max Visits</label><input type="number" name="max_visits" class="form-control" /></div>
            </div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="2" class="form-control"></textarea></div>

            <div class="form-check mb-2"><input type="checkbox" name="allow_partial_payment" value="1" id="ap" x-model="allowPartial" class="form-check-input"><label for="ap" class="form-check-label">Allow Partial Payment</label></div>
            <div class="row" x-show="allowPartial">
                <div class="col-md-6 form-group"><label>Min Deposit (RM)</label><input type="number" step="0.01" name="min_deposit_amount" class="form-control" /></div>
                <div class="col-md-6 form-group"><label>Min Deposit (%)</label><input type="number" step="0.01" name="min_deposit_percent" class="form-control" /></div>
            </div>

            <h5 class="mt-3">Package Items</h5>
            <template x-for="(it, idx) in items" :key="idx">
                <div class="row mb-2">
                    <div class="col-md-2"><select :name="'items['+idx+'][item_type]'" x-model="it.item_type" class="form-control"><option value="consultation">Consultation</option><option value="lab">Lab</option><option value="medicine">Medicine</option><option value="service">Service</option></select></div>
                    <div class="col-md-5"><input type="text" :name="'items['+idx+'][description]'" required x-model="it.description" class="form-control" placeholder="Item description" /></div>
                    <div class="col-md-2"><input type="number" :name="'items['+idx+'][quantity]'" x-model.number="it.quantity" min="1" required class="form-control" placeholder="Qty" /></div>
                    <div class="col-md-2"><input type="number" step="0.01" :name="'items['+idx+'][unit_value]'" x-model.number="it.unit_value" class="form-control" placeholder="Value" /></div>
                    <div class="col-md-1"><button type="button" @click="items.splice(idx, 1)" class="btn btn-danger btn-sm" x-show="items.length > 1">×</button></div>
                </div>
            </template>
            <button type="button" @click="items.push({ item_type: 'consultation', description: '', quantity: 1, unit_value: 0 })" class="btn btn-outline-primary btn-sm mb-3">+ Add Item</button>

            <div class="d-flex justify-content-end">
                <a href="{{ route('service-packages.index') }}" class="btn btn-light mr-2">Cancel</a>
                <button class="btn btn-primary">Create Package</button>
            </div>
        </form>
    </div></div>
</x-app-layout>
