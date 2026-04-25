<x-app-layout>
    <x-slot name="header"><h4 class="font-weight-bold mb-0">Prescription #{{ $prescription->id }}</h4></x-slot>

    <div class="row mb-4">
        <div class="card"><div class="card-body">
            <dl class="text-sm">
                <div><dt class="text-muted">Patient</dt><dd class="font-medium text-lg">{{ $prescription->patient->name }}</dd></div>
                <div><dt class="text-muted">Doctor</dt><dd>{{ $prescription->doctor->user->name ?? '-' }}</dd></div>
                @if($prescription->appointment)
                    <div><dt class="text-muted">Appointment</dt><dd>{{ $prescription->appointment->appointment_date->format('d M Y') }} {{ $prescription->appointment->start_time }}</dd></div>
                @endif
                <div><dt class="text-muted">Status</dt>
                    <dd>
                        @php $colors = ['draft' => 'badge-warning', 'dispensed' => 'badge-success', 'cancelled' => 'badge-danger']; @endphp
                        <span class="badge {{ $colors[$prescription->status] ?? 'badge-secondary' }}">{{ ucfirst($prescription->status) }}</span>
                    </dd>
                </div>
                <div><dt class="text-muted">Notes</dt><dd>{{ $prescription->notes ?? '-' }}</dd></div>
                <div><dt class="text-muted">Created</dt><dd>{{ $prescription->created_at->format('d M Y H:i') }}</dd></div>
            </dl>

            @if($prescription->status === 'draft')
                <div class="mt-3 d-flex gap-2">
                    <form method="POST" action="{{ route('prescriptions.dispense', $prescription) }}" onsubmit="return confirm('Dispense? Stock will be deducted.')">
                        @csrf @method('PATCH')
                        <button class="btn btn-success btn-sm">Dispense</button>
                    </form>
                    <form method="POST" action="{{ route('prescriptions.destroy', $prescription) }}" onsubmit="return confirm('Delete this prescription?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card"><div class="card-body">
            <h3 class="card-title">Prescribed Medicines</h3>
            <table class="table table-hover">
                <thead><tr>
                    <th class="text-left py-2">Medicine</th>
                    <th class="text-left py-2">Dosage</th>
                    <th class="text-left py-2">Frequency</th>
                    <th class="text-left py-2">Duration</th>
                    <th class="text-left py-2">Qty</th>
                </tr></thead>
                <tbody>
                    @foreach($prescription->items as $item)
                        <tr class="border-t">
                            <td class="py-2 font-medium">{{ $item->medicine->name }}</td>
                            <td class="py-2">{{ $item->dosage }}</td>
                            <td class="py-2">{{ $item->frequency }}</td>
                            <td class="py-2">{{ $item->duration }}</td>
                            <td class="py-2">{{ $item->quantity }}</td>
                        </tr>
                        @if($item->instructions)
                            <tr><td colspan="5" class="py-1 text-xs text-muted italic pl-4">{{ $item->instructions }}</td></tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('prescriptions.index') }}" class="btn btn-light btn-sm">Back to Prescriptions</a>
</x-app-layout>
