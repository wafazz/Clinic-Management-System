<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">New Prescription</h4></x-slot>

    <div class="card"><div class="card-body">
            <form method="POST" action="{{ route('prescriptions.store') }}" x-data="prescriptionForm()" >
                @csrf
                <div class="row">
                    <div>
                        <label class="form-label">Patient *</label>
                        <select name="patient_id" required class="form-control">
                            <option value="">Select Patient</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" {{ old('patient_id', $selectedPatient) == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->patient_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Doctor *</label>
                        <select name="doctor_id" required class="form-control">
                            <option value="">Select Doctor</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <input type="hidden" name="appointment_id" value="{{ $selectedAppointment }}" />

                <div>
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                </div>

                <h3 class="font-weight-bold pt-2">Medicines</h3>

                <template x-for="(item, index) in items" :key="index">
                    <div class="border rounded p-3 mb-2 position-relative">
                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-danger text-sm float-right">&times; Remove</button>
                        <div class="row">
                            <div>
                                <label class="text-xs text-muted">Medicine *</label>
                                <select :name="'items['+index+'][medicine_id]'" required class="form-control">
                                    <option value="">Select</option>
                                    @foreach($medicines as $med)
                                        <option value="{{ $med->id }}">{{ $med->name }} (Stock: {{ $med->current_stock }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted">Dosage *</label>
                                <input type="text" :name="'items['+index+'][dosage]'" x-model="item.dosage" required class="form-control" placeholder="e.g., 500mg" />
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <label class="text-xs text-muted">Frequency *</label>
                                <input type="text" :name="'items['+index+'][frequency]'" x-model="item.frequency" required class="form-control" placeholder="e.g., 3 times daily" />
                            </div>
                            <div>
                                <label class="text-xs text-muted">Duration *</label>
                                <input type="text" :name="'items['+index+'][duration]'" x-model="item.duration" required class="form-control" placeholder="e.g., 5 days" />
                            </div>
                            <div>
                                <label class="text-xs text-muted">Quantity *</label>
                                <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" required min="1" class="form-control" />
                            </div>
                        </div>
                        <div>
                            <label class="text-xs text-muted">Instructions</label>
                            <input type="text" :name="'items['+index+'][instructions]'" x-model="item.instructions" class="form-control" placeholder="e.g., After meals" />
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()" class="text-primary text-sm hover:underline">+ Add Medicine</button>

                <div class="flex space-x-3 pt-2">
                    <button type="submit" class="btn btn-primary btn-sm">Create Prescription</button>
                    <a href="{{ route('prescriptions.index') }}" class="btn btn-light btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function prescriptionForm() {
            return {
                items: [{ medicine_id: '', dosage: '', frequency: '', duration: '', quantity: 1, instructions: '' }],
                addItem() {
                    this.items.push({ medicine_id: '', dosage: '', frequency: '', duration: '', quantity: 1, instructions: '' });
                },
                removeItem(index) {
                    this.items.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
