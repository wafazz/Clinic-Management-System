<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">WhatsApp Reminders</h4>
            <a href="{{ route('reminders.create') }}" class="btn btn-success btn-sm">New Reminder</a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded mb-6">
        <div class="card-body">
            <h3 class="font-weight-bold mb-3">Bulk Send Reminders</h3>
            <form method="POST" action="{{ route('reminders.bulk') }}" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs text-muted">Days Before Appointment</label>
                    <select name="days_before" required class="rounded shadow-sm text-sm">
                        @for($d = 1; $d <= 7; $d++)
                            <option value="{{ $d }}">{{ $d }} day{{ $d > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted">Channel</label>
                    <select name="channel" required class="rounded shadow-sm text-sm">
                        <option value="whatsapp">WhatsApp</option>
                        <option value="sms">SMS</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-muted">Message Template</label>
                    <input type="text" name="message_template" required class="form-control" value="Hi {patient_name}, reminder for your appointment on {date} at {time} with Dr. {doctor}. Please arrive 10 minutes early." />
                </div>
                <button type="submit" class="btn btn-success btn-sm">Create Bulk Reminders</button>
            </form>
            <p class="text-xs text-muted mt-2">Variables: {patient_name}, {date}, {time}, {doctor}</p>
        </div>
    </div>

    <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-center gap-2">
                <select name="status" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Status</option>
                    @foreach(['pending', 'sent', 'failed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="channel" class="form-control form-control-sm" style="max-width:150px">
                    <option value="">All Channels</option>
                    <option value="whatsapp" {{ request('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="sms" {{ request('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
            </form>
            <table class="table table-striped table-hover">
                <thead ><tr>
                    <th >Patient</th>
                    <th >Appt Date</th>
                    <th >Phone</th>
                    <th >Channel</th>
                    <th >Status</th>
                    <th >Scheduled</th>
                    <th >Actions</th>
                </tr></thead>
                <tbody >
                    @forelse($reminders as $rem)
                        <tr>
                            <td >{{ $rem->appointment->patient->name ?? '-' }}</td>
                            <td >{{ $rem->appointment->appointment_date->format('d M Y') }} {{ $rem->appointment->start_time }}</td>
                            <td >{{ $rem->phone_number }}</td>
                            <td ><span class="badge badge-success">{{ ucfirst($rem->channel) }}</span></td>
                            <td >
                                @php $colors = ['pending' => 'badge-warning', 'sent' => 'badge-success', 'failed' => 'badge-danger']; @endphp
                                <span class="badge {{ $colors[$rem->status] ?? 'badge-secondary' }}">{{ ucfirst($rem->status) }}</span>
                            </td>
                            <td >{{ $rem->scheduled_at->format('d M Y H:i') }}</td>
                            <td >
                                @if($rem->status === 'pending')
                                    <form method="POST" action="{{ route('reminders.send', $rem) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="text-success hover:underline">Send Now</button>
                                    </form>
                                    <form method="POST" action="{{ route('reminders.destroy', $rem) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm py-1 px-2">Delete</button>
                                    </form>
                                @elseif($rem->status === 'failed')
                                    <form method="POST" action="{{ route('reminders.send', $rem) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button class="text-orange-600 hover:underline">Retry</button>
                                    </form>
                                @else
                                    <span class="text-muted text-xs">Sent {{ $rem->sent_at?->diffForHumans() }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No reminders found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $reminders->links() }}</div>
        </div>
    </div>
</x-app-layout>
