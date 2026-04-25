<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Edit Invoice - {{ $invoice->invoice_number }}</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('invoices.update', $invoice) }}"  x-data="invoiceForm()">
                @csrf @method('PUT')

                <h3 class="text-lg font-weight-bold border-b pb-2">Line Items</h3>
                <template x-for="(item, index) in items" :key="index">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <select :name="'items['+index+'][service_id]'" x-on:change="prefillService($event, index)" class="form-control">
                                <option value="">Custom</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" :name="'items['+index+'][description]'" x-model="item.description" required class="form-control" />
                        </div>
                        <div class="col-md-2">
                            <input type="number" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" min="1" required class="form-control" @input="calcTotal" />
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model.number="item.unit_price" min="0" required class="form-control" @input="calcTotal" />
                        </div>
                        <div class="col-md-1 text-sm font-medium" x-text="'RM ' + (item.quantity * item.unit_price).toFixed(2)"></div>
                        <div class="col-md-1">
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-danger text-sm">X</button>
                        </div>
                    </div>
                </template>
                <button type="button" @click="addItem()" class="text-primary text-sm font-medium">+ Add Item</button>

                <div class="row border-top pt-3">
                    <div>
                        <label class="form-label">Tax (RM)</label>
                        <input type="number" step="0.01" name="tax" x-model.number="tax" class="form-control" @input="calcTotal" />
                    </div>
                    <div>
                        <label class="form-label">Discount (RM)</label>
                        <input type="number" step="0.01" name="discount" x-model.number="discount" class="form-control" @input="calcTotal" />
                    </div>
                    <div class="d-flex align-items-end">
                        <div class="h5 font-bold">Total: RM <span x-text="grandTotal.toFixed(2)">0.00</span></div>
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary btn-sm">Update Invoice</button>
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function invoiceForm() {
            return {
                items: @json($invoice->items->map(fn($i) => ['description' => $i->description, 'quantity' => $i->quantity, 'unit_price' => (float)$i->unit_price])),
                tax: {{ $invoice->tax }},
                discount: {{ $invoice->discount }},
                grandTotal: {{ $invoice->total }},
                addItem() { this.items.push({ description: '', quantity: 1, unit_price: 0 }); },
                removeItem(i) { this.items.splice(i, 1); this.calcTotal(); },
                prefillService(e, index) {
                    let opt = e.target.selectedOptions[0];
                    if (opt.dataset.name) {
                        this.items[index].description = opt.dataset.name;
                        this.items[index].unit_price = parseFloat(opt.dataset.price) || 0;
                        this.calcTotal();
                    }
                },
                calcTotal() {
                    let sub = this.items.reduce((s, i) => s + (i.quantity * i.unit_price), 0);
                    this.grandTotal = sub + (this.tax || 0) - (this.discount || 0);
                }
            }
        }
    </script>
</x-app-layout>
