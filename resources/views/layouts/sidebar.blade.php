<nav class="sidebar sidebar-offcanvas dynamic-active-class-disabled" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="{{ route('profile.edit') }}" class="nav-link">
                <div class="profile-image" style="position:relative;">
                    @if(Auth::user()->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" style="width:35px;height:35px;border-radius:50%;object-fit:cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="width:35px;height:35px;border-radius:50%;background:#6c63ff;color:#fff;font-weight:700;font-size:14px;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="dot-indicator bg-success" style="position:absolute;bottom:0;right:0;"></div>
                </div>
                <div class="text-wrapper">
                    <p class="profile-name">{{ Auth::user()->name }}</p>
                    <p class="designation">{{ ucfirst(Auth::user()->role ?? 'Staff') }}</p>
                </div>
            </a>
        </li>

        <li class="nav-item nav-category">Main</li>

        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="menu-icon mdi mdi-monitor-dashboard"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('branches.index') }}">
                <i class="menu-icon mdi mdi-office-building"></i>
                <span class="menu-title">Branches</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('patients.index') }}">
                <i class="menu-icon mdi mdi-account-multiple"></i>
                <span class="menu-title">Patients</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('doctors.*') || request()->routeIs('doctor-schedules.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('doctors.index') }}">
                <i class="menu-icon mdi mdi-stethoscope"></i>
                <span class="menu-title">Doctors</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('appointments.index') }}">
                <i class="menu-icon mdi mdi-calendar-clock"></i>
                <span class="menu-title">Appointments</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('walk-in-queue.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('walk-in-queue.index') }}">
                <i class="menu-icon mdi mdi-ticket-confirmation"></i>
                <span class="menu-title">Nombor Giliran</span>
                @php $waitingCount = \App\Models\WalkInQueue::where('branch_id', session('current_branch_id'))->whereDate('queue_date', today())->where('status', 'waiting')->count(); @endphp
                @if($waitingCount > 0)
                    <span class="badge badge-warning ml-auto">{{ $waitingCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item nav-category">Billing & Services</li>

        <li class="nav-item {{ request()->routeIs('services.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('services.index') }}">
                <i class="menu-icon mdi mdi-medical-bag"></i>
                <span class="menu-title">Services</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('invoices.index') }}">
                <i class="menu-icon mdi mdi-receipt"></i>
                <span class="menu-title">Billing</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('insurance-panels.*') || request()->routeIs('insurance-claims.*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#insurance-menu" aria-expanded="{{ request()->routeIs('insurance-panels.*') || request()->routeIs('insurance-claims.*') ? 'true' : 'false' }}">
                <i class="menu-icon mdi mdi-shield-check"></i>
                <span class="menu-title">Insurance</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('insurance-panels.*') || request()->routeIs('insurance-claims.*') ? 'show' : '' }}" id="insurance-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('insurance-panels.*') ? 'active' : '' }}" href="{{ route('insurance-panels.index') }}"><i class="mdi mdi-domain mr-2"></i>Panels</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('insurance-claims.*') ? 'active' : '' }}" href="{{ route('insurance-claims.index') }}"><i class="mdi mdi-file-document mr-2"></i>Claims</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item nav-category">Clinical</li>

        <li class="nav-item {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('consultations.index') }}">
                <i class="menu-icon mdi mdi-stethoscope"></i>
                <span class="menu-title">Consultations</span>
                @php $inProgressCount = \App\Models\Consultation::where('branch_id', session('current_branch_id'))->where('status', 'in_progress')->count(); @endphp
                @if($inProgressCount > 0)
                    <span class="badge badge-warning ml-auto">{{ $inProgressCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('medicines.*') || request()->routeIs('pharmacy-categories.*') || request()->routeIs('prescriptions.*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#pharmacy-menu" aria-expanded="{{ request()->routeIs('medicines.*') || request()->routeIs('pharmacy-categories.*') || request()->routeIs('prescriptions.*') ? 'true' : 'false' }}">
                <i class="menu-icon mdi mdi-pill"></i>
                <span class="menu-title">Pharmacy</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('medicines.*') || request()->routeIs('pharmacy-categories.*') || request()->routeIs('prescriptions.*') ? 'show' : '' }}" id="pharmacy-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('medicines.*') ? 'active' : '' }}" href="{{ route('medicines.index') }}"><i class="mdi mdi-pill mr-2"></i>Medicines</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('pharmacy-categories.*') ? 'active' : '' }}" href="{{ route('pharmacy-categories.index') }}"><i class="mdi mdi-folder mr-2"></i>Categories</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}" href="{{ route('prescriptions.index') }}"><i class="mdi mdi-clipboard-text mr-2"></i>Prescriptions</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('lab-reports.*') || request()->routeIs('lab-tests.*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#lab-menu" aria-expanded="{{ request()->routeIs('lab-reports.*') || request()->routeIs('lab-tests.*') ? 'true' : 'false' }}">
                <i class="menu-icon mdi mdi-flask"></i>
                <span class="menu-title">Lab</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('lab-reports.*') || request()->routeIs('lab-tests.*') ? 'show' : '' }}" id="lab-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('lab-reports.*') ? 'active' : '' }}" href="{{ route('lab-reports.index') }}"><i class="mdi mdi-file-chart mr-2"></i>Reports</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('lab-tests.*') ? 'active' : '' }}" href="{{ route('lab-tests.index') }}"><i class="mdi mdi-test-tube mr-2"></i>Tests</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item nav-category">Others</li>

        <li class="nav-item {{ request()->routeIs('locum-doctors.*') || request()->routeIs('locum-sessions.*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#locum-menu" aria-expanded="{{ request()->routeIs('locum-doctors.*') || request()->routeIs('locum-sessions.*') ? 'true' : 'false' }}">
                <i class="menu-icon mdi mdi-account-switch"></i>
                <span class="menu-title">Locum</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('locum-doctors.*') || request()->routeIs('locum-sessions.*') ? 'show' : '' }}" id="locum-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('locum-doctors.*') ? 'active' : '' }}" href="{{ route('locum-doctors.index') }}"><i class="mdi mdi-account-card-details mr-2"></i>Doctors</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('locum-sessions.*') ? 'active' : '' }}" href="{{ route('locum-sessions.index') }}"><i class="mdi mdi-calendar-check mr-2"></i>Sessions</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('reminders.index') }}">
                <i class="menu-icon mdi mdi-bell-ring"></i>
                <span class="menu-title">Reminders</span>
            </a>
        </li>

        <li class="nav-item nav-category">Reports</li>

        <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a class="nav-link" data-toggle="collapse" href="#reports-menu" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                <i class="menu-icon mdi mdi-chart-bar"></i>
                <span class="menu-title">Reports</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reports-menu">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}" href="{{ route('reports.financial') }}"><i class="mdi mdi-cash-multiple mr-2"></i>Financial</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.patients') ? 'active' : '' }}" href="{{ route('reports.patients') }}"><i class="mdi mdi-account-multiple mr-2"></i>Patients</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.appointments') ? 'active' : '' }}" href="{{ route('reports.appointments') }}"><i class="mdi mdi-calendar-clock mr-2"></i>Appointments</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.pharmacy') ? 'active' : '' }}" href="{{ route('reports.pharmacy') }}"><i class="mdi mdi-pill mr-2"></i>Pharmacy</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('reports.lab') ? 'active' : '' }}" href="{{ route('reports.lab') }}"><i class="mdi mdi-flask mr-2"></i>Lab</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item nav-category">System</li>

        <li class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('notifications.index') }}">
                <i class="menu-icon mdi mdi-bell-outline"></i>
                <span class="menu-title">Notifications</span>
                @php $sidebarUnread = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count(); @endphp
                @if($sidebarUnread > 0)
                    <span class="badge badge-danger ml-auto">{{ $sidebarUnread }}</span>
                @endif
            </a>
        </li>

        @if(auth()->user()->isAdmin())
        <li class="nav-item {{ request()->routeIs('audit-logs.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('audit-logs.index') }}">
                <i class="menu-icon mdi mdi-history"></i>
                <span class="menu-title">Audit Logs</span>
            </a>
        </li>

        <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('users.index') }}">
                <i class="menu-icon mdi mdi-account-key"></i>
                <span class="menu-title">User Management</span>
            </a>
        </li>
        @endif

        <li class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('settings.index') }}">
                <i class="menu-icon mdi mdi-settings"></i>
                <span class="menu-title">Settings</span>
            </a>
        </li>
    </ul>
</nav>
