<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; background: #f0f2f5; }
        .sidebar { width: 260px; min-height: 100vh; background: linear-gradient(135deg, #1a1c2e 0%, #2d3154 100%); position: fixed; right: 0; top: 0; z-index: 100; transition: all .3s; }
        .sidebar .nav-link { color: rgba(255,255,255,.7); padding: 12px 20px; border-radius: 8px; margin: 2px 10px; font-size: 14px; transition: all .2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-link i { width: 24px; margin-left: 8px; }
        .sidebar-brand { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar-brand h5 { color: #fff; margin: 0; font-size: 16px; }
        .main-content { margin-right: 260px; padding: 20px; }
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.08); transition: transform .2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .page-header { background: #fff; padding: 20px 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.04); }
        .table th { font-weight: 600; font-size: 13px; color: #6c757d; text-transform: uppercase; border-bottom: 2px solid #e9ecef; }
        .badge { font-weight: 500; padding: 6px 12px; }
        .btn { border-radius: 8px; font-size: 14px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-right: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h5><i class="bi bi-trophy"></i> منصة التصويت الرياضي</h5>
            <small class="text-white-50">{{ auth()->user()->hasRole('super-admin') ? 'الجمعية' : (auth()->user()->club->name_ar ?? '') }}</small>
        </div>
        <nav class="mt-3">
            @if(auth()->user()->hasRole('super-admin'))
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> لوحة التحكم
                </a>
                <a href="{{ route('admin.leagues.index') }}" class="nav-link {{ request()->routeIs('admin.leagues.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i> الدوريات
                </a>
                <a href="{{ route('admin.clubs.index') }}" class="nav-link {{ request()->routeIs('admin.clubs.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> الأندية
                </a>
                <a href="{{ route('admin.admins.index') }}" class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> الإداريون
                </a>
                <a href="{{ route('admin.players.index') }}" class="nav-link {{ request()->routeIs('admin.players.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> اللاعبون
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="nav-link {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> حملات التصويت
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> سجل العمليات
                </a>
                <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> الإعدادات
                </a>
            @endif

            @if(auth()->user()->hasRole('club-admin'))
                <a href="{{ route('club.dashboard') }}" class="nav-link {{ request()->routeIs('club.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> لوحة التحكم
                </a>
                <a href="{{ route('club.players.index') }}" class="nav-link {{ request()->routeIs('club.players.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> اللاعبون
                </a>
                <a href="{{ route('club.campaigns.index') }}" class="nav-link {{ request()->routeIs('club.campaigns.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> حملات التصويت
                </a>
                <a href="{{ route('club.profile') }}" class="nav-link {{ request()->routeIs('club.profile') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> ملف النادي
                </a>
            @endif

            <hr class="border-secondary mx-3">
            <a href="{{ route('profile.edit') }}" class="nav-link">
                <i class="bi bi-person-gear"></i> الملف الشخصي
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                    <i class="bi bi-box-arrow-right"></i> تسجيل الخروج
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <span class="text-muted"><i class="bi bi-person-circle"></i> {{ auth()->user()->name }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
