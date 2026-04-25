@php
    $clinicLogo = \App\Models\Setting::get('clinic_logo');
    $clinicName = \App\Models\Setting::get('clinic_name', 'CMS');
@endphp
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">
        <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}">
            @if($clinicLogo)
                <img src="{{ asset('storage/' . $clinicLogo) }}" alt="{{ $clinicName }}" style="max-height:40px; max-width:160px;" />
            @else
                <span style="font-size:20px;font-weight:700;color:#fff;">{{ $clinicName }}</span>
            @endif
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
            @if($clinicLogo)
                <img src="{{ asset('storage/' . $clinicLogo) }}" alt="{{ $clinicName }}" style="max-height:30px; max-width:40px;" />
            @else
                <span style="font-size:16px;font-weight:700;color:#fff;">{{ Str::limit($clinicName, 1, '') }}</span>
            @endif
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center">
        <ul class="navbar-nav">
            <li class="nav-item font-weight-semibold d-none d-lg-block">
                {{ \App\Models\Setting::get('clinic_name', 'Clinic Management System') }}
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            {{-- Branch Switcher --}}
            @php
                $branches = \App\Models\Branch::where('is_active', true)->get();
                $currentBranch = \App\Models\Branch::find(session('current_branch_id'));
            @endphp
            @if($branches->count() > 0)
                <li class="nav-item">
                    <form method="POST" action="{{ route('branch.switch') }}" class="d-flex align-items-center mt-1">
                        @csrf
                        <select name="branch_id" onchange="this.form.submit()" class="form-control form-control-sm" style="width:180px;">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ session('current_branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </li>
            @endif

            {{-- Notifications Bell --}}
            @php
                $unreadNotifCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
                $recentNotifs = \App\Models\Notification::where('user_id', auth()->id())->orderByDesc('created_at')->limit(5)->get();
            @endphp
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-bell-outline" style="font-size:22px;"></i>
                    @if($unreadNotifCount > 0)
                        <span class="count bg-danger text-white" style="position:absolute;top:4px;right:0;font-size:10px;border-radius:50%;width:18px;height:18px;display:flex;align-items:center;justify-content:center;">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" style="width:320px;max-height:400px;overflow-y:auto;">
                    <p class="mb-0 font-weight-medium float-left dropdown-header">Notifications</p>
                    @forelse($recentNotifs as $rn)
                        <form method="POST" action="{{ route('notifications.read', $rn) }}" class="dropdown-item preview-item d-flex align-items-start" style="cursor:pointer;" onsubmit="return true;">
                            @csrf
                            <div class="preview-thumbnail">
                                <i class="mdi {{ $rn->icon }} text-{{ $rn->color }}" style="font-size:20px;"></i>
                            </div>
                            <div class="preview-item-content ml-2 flex-grow-1">
                                <h6 class="preview-subject mb-0 {{ $rn->read_at ? 'text-muted' : 'font-weight-bold' }}" style="font-size:13px;">{{ Str::limit($rn->title, 35) }}</h6>
                                <p class="text-muted mb-0" style="font-size:11px;">{{ Str::limit($rn->message, 50) }}</p>
                                <small class="text-muted">{{ $rn->created_at->diffForHumans() }}</small>
                            </div>
                            <button type="submit" class="btn btn-link p-0 ml-1" style="font-size:11px;text-decoration:none;">
                                @if(!$rn->read_at)<i class="mdi mdi-circle text-primary" style="font-size:8px;"></i>@endif
                            </button>
                        </form>
                    @empty
                        <div class="dropdown-item text-center text-muted py-3">No notifications</div>
                    @endforelse
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center small" href="{{ route('notifications.index') }}">View all notifications</a>
                </div>
            </li>

            {{-- User Dropdown --}}
            <li class="nav-item dropdown d-none d-xl-inline-block user-dropdown">
                <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                    <span class="font-weight-medium mr-2">{{ Auth::user()->name }}</span>
                    <i class="mdi mdi-chevron-down"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->name }}</p>
                        <p class="font-weight-light text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        My Profile <i class="dropdown-item-icon mdi mdi-account-outline"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                            Sign Out <i class="dropdown-item-icon mdi mdi-power"></i>
                        </a>
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
