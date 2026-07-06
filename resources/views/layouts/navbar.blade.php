<nav class="top-nav navbar navbar-expand-lg navbar-light shadow-sm sticky-top">
    <div class="container-fluid px-0">
        <!-- Sidebar Toggle -->
        <button type="button" id="sidebarCollapse" class="btn btn-link text-dark p-0 me-3">
            <i class="fas fa-bars fs-5"></i>
        </button>

        <!-- Dynamic Date / Title -->
        <div class="d-none d-md-flex flex-column align-items-start">
            <span class="text-secondary small">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            <span class="fw-semibold text-primary">Periode: {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
        </div>

        <div class="ms-auto d-flex align-items-center">
            @php
                $theme = $theme ?? (auth()->check() ? auth()->user()->getOrCreateSetting()->theme : 'light');
            @endphp
            <!-- Theme Toggle -->
            <button id="theme-toggle" class="btn btn-link theme-toggle-btn me-3 p-0">
                <i class="fas {{ $theme === 'dark' ? 'fa-sun text-warning' : 'fa-moon' }} fs-5"></i>
            </button>

            <!-- Notifications Dropdown -->
            @php
                $unreadNotifications = auth()->user()->notifications()->unread()->latest()->take(5)->get();
                $unreadCount = auth()->user()->notifications()->unread()->count();
            @endphp
            <div class="dropdown me-3">
                <button class="btn btn-link position-relative text-dark p-0" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="far fa-bell fs-5"></i>
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 py-0" aria-labelledby="notificationDropdown" style="width: 320px; border-radius: 12px; overflow: hidden;">
                    <div class="bg-primary text-white p-3 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Notifikasi</span>
                        @if($unreadCount > 0)
                            <a href="{{ route('notifications.readAll') }}" class="text-white text-decoration-none small">Tandai semua dibaca</a>
                        @endif
                    </div>
                    <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                        @forelse($unreadNotifications as $notif)
                            <a href="{{ route('notifications.show', $notif->id) }}" class="list-group-item list-group-item-action p-3">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <h6 class="mb-1 fw-bold text-truncate" style="max-width: 180px;">{{ $notif->title }}</h6>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 text-muted small text-truncate">{{ $notif->message }}</p>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <i class="far fa-bell-slash fs-2 mb-2 opacity-50"></i>
                                <p class="mb-0 small">Tidak ada notifikasi baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="dropdown">
                <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="rounded-circle border" width="36" height="36" style="object-fit: cover;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="ms-2 d-none d-lg-inline fw-semibold text-primary">{{ auth()->user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="profileDropdown" style="border-radius: 12px;">
                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2 text-muted"></i>Profil Saya</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2 text-muted"></i>Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
