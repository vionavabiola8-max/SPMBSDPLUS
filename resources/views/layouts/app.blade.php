<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SPMB SD Plus Darul Ulum</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-dark: #0f172a;
            --primary-light: #3b82f6;
            --secondary: #0d9488;
            --accent: #f59e0b;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
            --card-bg: #ffffff;
            --body-bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #cbd5e1;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.6);
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-category {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 700;
            padding: 0.8rem 0.75rem 0.4rem;
        }

        .sidebar-nav .nav-link {
            color: #94a3b8;
            padding: 0.7rem 0.9rem;
            border-radius: 0.5rem;
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-nav .nav-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
        }

        .sidebar-nav .nav-link:hover i {
            color: #38bdf8;
        }

        .sidebar-nav .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            font-weight: 600;
        }

        .sidebar-nav .nav-link.active i {
            color: #ffffff;
        }

        /* Main Content Wrapper */
        #content-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Top Navbar */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        /* Content Area */
        .page-content {
            padding: 1.75rem;
            flex-grow: 1;
        }

        /* Modern Card */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .stat-card {
            border-radius: 0.85rem;
            padding: 1.25rem;
            border: 1px solid var(--border-color);
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* EWS Badges & Pulses */
        .badge-ews-overload {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .badge-ews-waspada {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .badge-ews-aman {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .pulse-danger {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ef4444;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 1.6s infinite;
        }

        .pulse-warning {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #f59e0b;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            animation: pulse-yellow 1.6s infinite;
        }

        .pulse-success {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 1.6s infinite;
        }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        @keyframes pulse-yellow {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(245, 158, 11, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        @keyframes pulse-green {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Buttons & Tables */
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
            font-weight: 500;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4);
        }

        .table > :not(caption) > * > * {
            padding: 0.85rem 1rem;
            vertical-align: middle;
        }

        .table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--border-color);
        }

        /* Responsive Mobile Drawer */
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: -280px;
            }
            #sidebar.show {
                margin-left: 0;
            }
            #content-wrapper {
                margin-left: 0;
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(2px);
                z-index: 1035;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-mortarboard-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-white tracking-wide">SPMB SD PLUS</h6>
                    <small class="text-xs text-primary-light fw-medium">Darul Ulum Forecasting</small>
                </div>
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-category">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard Interaktif</span>
            </a>
            <a href="{{ route('forecasting.index') }}" class="nav-link {{ request()->routeIs('forecasting.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>Proyeksi & EWS</span>
            </a>
            <a href="{{ route('evaluation.index') }}" class="nav-link {{ request()->routeIs('evaluation.*') ? 'active' : '' }}">
                <i class="bi bi-cpu-fill"></i>
                <span>Komparasi Model ML</span>
            </a>

            <div class="nav-category">Data Pipeline</div>
            <a href="{{ route('historis.index') }}" class="nav-link {{ request()->routeIs('historis.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Historis Pendaftaran</span>
            </a>
            <a href="{{ route('demografi.index') }}" class="nav-link {{ request()->routeIs('demografi.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i>
                <span>Demografi Wilayah</span>
            </a>
            <a href="{{ route('kapasitas.index') }}" class="nav-link {{ request()->routeIs('kapasitas.*') ? 'active' : '' }}">
                <i class="bi bi-sliders2"></i>
                <span>Manajemen Kapasitas</span>
            </a>

            <div class="nav-category">Output & Keputusan</div>
            <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-pdf-fill"></i>
                <span>Cetak Cetak Biru PDF</span>
            </a>
        </div>

        <!-- Sidebar Footer Status -->
        <div class="p-3 border-top border-secondary border-opacity-25 bg-black bg-opacity-25">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-xs text-muted d-block">Algoritma Aktif</small>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        <i class="bi bi-check2-circle me-1"></i> XGBoost ML
                    </span>
                </div>
                <form action="{{ route('forecasting.retrain') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light rounded-pill px-2 py-1 text-xs" title="Latih Ulang Model">
                        <i class="bi bi-arrow-repeat"></i> Retrain
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div id="content-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none border p-2" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="mb-0 fw-bold">@yield('page_heading', 'Dashboard SPMB')</h5>
                    <small class="text-muted">SD Plus Darul Ulum - Decision Support System</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Academic Skripsi Bab 4 Quick Tag -->
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-none d-sm-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill">
                    <i class="bi bi-journal-code"></i> Model: XGBoost vs Moving Average
                </span>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light border d-flex align-items-center gap-2 rounded-pill px-3 py-1" type="button" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.8rem;">
                            TU
                        </div>
                        <span class="small fw-semibold d-none d-sm-inline">Staf TU / Kepala Sekolah</span>
                        <i class="bi bi-chevron-down text-muted small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border mt-2">
                        <li><h6 class="dropdown-header">SD Plus Darul Ulum</h6></li>
                        <li><span class="dropdown-item-text small text-muted">admin@sdplusdarululum.sch.id</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small" href="{{ route('kapasitas.index') }}"><i class="bi bi-sliders me-2"></i> Pengaturan Kapasitas</a></li>
                        <li><a class="dropdown-item small" href="{{ route('laporan.index') }}"><i class="bi bi-file-earmark-pdf me-2"></i> Cetak Laporan</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="page-content">
            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Terjadi Kesalahan Validasi:</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto bg-white border-top py-3 px-4 text-center text-muted small">
            <div>&copy; {{ date('Y') }} Sistem Proyeksi Pendaftaran Siswa Baru (SPMB) - SD Plus Darul Ulum. Powered by XGBoost Machine Learning Engine.</div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Mobile Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            });
        }

        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', () => {
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
