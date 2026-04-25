<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0">Notifications</h4>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="mdi mdi-check-all"></i> Mark All Read ({{ $unreadCount }})
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="row align-items-center">
                <div class="col-auto">
                    <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="appointment" {{ request('type') == 'appointment' ? 'selected' : '' }}>Appointments</option>
                        <option value="invoice" {{ request('type') == 'invoice' ? 'selected' : '' }}>Invoices</option>
                        <option value="pharmacy" {{ request('type') == 'pharmacy' ? 'selected' : '' }}>Pharmacy</option>
                        <option value="lab" {{ request('type') == 'lab' ? 'selected' : '' }}>Lab</option>
                        <option value="insurance" {{ request('type') == 'insurance' ? 'selected' : '' }}>Insurance</option>
                        <option value="system" {{ request('type') == 'system' ? 'selected' : '' }}>System</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @forelse($notifications as $notif)
                <div class="d-flex align-items-start p-3 border-bottom {{ $notif->is_read ? '' : 'bg-light' }}">
                    <div class="mr-3">
                        <span class="badge badge-{{ $notif->color }} p-2" style="font-size:18px;">
                            <i class="mdi {{ $notif->icon }}"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 {{ $notif->is_read ? 'text-muted' : 'font-weight-bold' }}">{{ $notif->title }}</h6>
                                <p class="mb-1 small {{ $notif->is_read ? 'text-muted' : '' }}">{{ $notif->message }}</p>
                                <small class="text-muted">
                                    <i class="mdi mdi-clock-outline"></i> {{ $notif->created_at->diffForHumans() }}
                                    <span class="badge badge-light ml-1">{{ ucfirst($notif->type) }}</span>
                                </small>
                            </div>
                            <div class="d-flex align-items-center ml-2">
                                @if(!$notif->is_read)
                                    <form method="POST" action="{{ route('notifications.read', $notif) }}" class="mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm py-0 px-1" title="Mark as read">
                                            <i class="mdi mdi-check"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($notif->link)
                                    <a href="{{ route('notifications.read', $notif) }}" class="btn btn-outline-info btn-sm py-0 px-1 mr-1" title="View">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('notifications.destroy', $notif) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-1" title="Delete">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5">
                    <i class="mdi mdi-bell-off" style="font-size:48px;"></i>
                    <p class="mt-2">No notifications</p>
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-3">{{ $notifications->links() }}</div>
</x-app-layout>
