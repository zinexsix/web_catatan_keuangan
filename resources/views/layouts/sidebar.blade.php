<aside id="sidebar">
    <!-- Brand Area -->
    <div class="brand d-flex align-items-center justify-content-between">
        <div class="brand-logo-container d-flex align-items-center">
            <!-- Blue Gradient Logo Box -->
            <div class="logo-box" style="background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%); width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                <i class="fas fa-sack-dollar text-warning fs-5"></i>
            </div>
            <div class="d-flex flex-column text-start brand-text">
                <span class="fw-bold text-white fs-6 leading-tight">Finance Manager</span>
                <span class="text-white-50" style="font-size: 0.65rem;">Smart Money Management</span>
            </div>
        </div>
        <!-- Close Button (Mobile Only) -->
        <button type="button" class="btn text-white-50 d-md-none p-0 border-0" id="sidebarCloseBtn" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-xmark fs-5"></i>
        </button>
    </div>

    <!-- User profile card in sidebar -->
    <div class="user-profile-sidebar py-4 px-3 text-center d-flex flex-column align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.08);">
        <!-- Circle Avatar with Initial -->
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold mb-2 shadow" style="width: 72px; height: 72px; font-size: 1.8rem; background: linear-gradient(135deg, #6366f1 0%, #3b82f6 100%); color: #fff;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="small text-white-50">Selamat datang,</div>
        <div class="fw-bold text-white fs-6 mt-1 text-truncate" style="max-width: 220px;">{{ auth()->user()->name }}</div>
        <div class="avatar-status-dot mt-2" title="Online"></div>
    </div>
    
    <!-- Menu List -->
    <ul class="menu-list">
        <!-- Dashboard -->
        <li class="menu-item {{ Route::is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-chart-column text-info"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Dashboard</span>
                    <span class="menu-subtitle">Overview & Analytics</span>
                </div>
            </a>
        </li>
        
        <!-- Tambah Transaksi -->
        <li class="menu-item {{ Route::is('transactions.create') ? 'active' : '' }}">
            <a href="{{ route('transactions.create') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-plus text-success"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Tambah Data</span>
                    <span class="menu-subtitle">Input Transaksi Baru</span>
                </div>
            </a>
        </li>

        <!-- Daftar Transaksi -->
        <li class="menu-item {{ Route::is('transactions.index') ? 'active' : '' }}">
            <a href="{{ route('transactions.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-clipboard-check text-warning"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Daftar Data</span>
                    <span class="menu-subtitle">Lihat Semua Transaksi</span>
                </div>
            </a>
        </li>

        <!-- Kategori -->
        <li class="menu-item {{ Route::is('categories.index') ? 'active' : '' }}">
            <a href="{{ route('categories.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-tags text-danger"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Kategori</span>
                    <span class="menu-subtitle">Kelola Kategori Keuangan</span>
                </div>
            </a>
        </li>

        <!-- Target Keuangan -->
        <li class="menu-item {{ Route::is('saving-targets.*') ? 'active' : '' }}">
            <a href="{{ route('saving-targets.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-piggy-bank text-primary"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Target Keuangan</span>
                    <span class="menu-subtitle">Rencanakan Tabungan Impian</span>
                </div>
            </a>
        </li>

        <!-- Budget Bulanan -->
        <li class="menu-item {{ Route::is('budgets.*') ? 'active' : '' }}">
            <a href="{{ route('budgets.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-sack-dollar text-success"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Budget Bulanan</span>
                    <span class="menu-subtitle">Batas Pengeluaran Bulanan</span>
                </div>
            </a>
        </li>

        <!-- Tagihan Rutin -->
        <li class="menu-item {{ Route::is('bills.*') ? 'active' : '' }}">
            <a href="{{ route('bills.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-file-invoice-dollar text-warning"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Tagihan Rutin</span>
                    <span class="menu-subtitle">Tagihan & Pengeluaran Wajib</span>
                </div>
            </a>
        </li>

        <!-- Laporan Keuangan -->
        <li class="menu-item {{ Route::is('reports.*') ? 'active' : '' }}">
            <a href="{{ route('reports.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-chart-line text-info"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Laporan</span>
                    <span class="menu-subtitle">Analisis & Grafik Keuangan</span>
                </div>
            </a>
        </li>

        <!-- Profil -->
        <li class="menu-item {{ Route::is('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-user-gear text-secondary"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Profil</span>
                    <span class="menu-subtitle">Pengaturan Akun & Sandi</span>
                </div>
            </a>
        </li>

        <!-- Pengaturan -->
        <li class="menu-item {{ Route::is('settings.*') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}" class="menu-link">
                <div class="menu-icon-container">
                    <i class="fa-solid fa-sliders text-light"></i>
                </div>
                <div class="menu-text-container">
                    <span class="menu-title">Pengaturan</span>
                    <span class="menu-subtitle">Preferensi & Backup</span>
                </div>
            </a>
        </li>

        <!-- Logout -->
        <li class="menu-item mt-4 mb-4">
            <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar">
                @csrf
                <a href="#" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                    <div class="menu-icon-container bg-danger-subtle">
                        <i class="fa-solid fa-right-from-bracket text-danger"></i>
                    </div>
                    <div class="menu-text-container">
                        <span class="menu-title text-danger">Logout</span>
                        <span class="menu-subtitle">Keluar dari Akun Anda</span>
                    </div>
                </a>
            </form>
        </li>
    </ul>
</aside>
