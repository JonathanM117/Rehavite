<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rehavite') — Panel Admin</title>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @livewireStyles
    @yield('css')
</head>
<body class="admin-layout">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">R</div>
            <span class="logo-text">Rehavité</span>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Principal</div>
                <a href="{{ route('admin.home') }}" class="nav-item {{ request()->routeIs('admin.home') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Gestión Clínica</div>
                <a href="{{ route('admin.patients.index') }}" class="nav-item {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i>
                    <span>Pacientes</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Sistema</div>
                <a href="{{ route('admin.users.profile') }}" class="nav-item {{ request()->routeIs('admin.users.profile') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-circle"></i>
                    <span>Mi Perfil</span>
                </a>
                @if(auth()->user()->admin)
                    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <span>Usuarios</span>
                    </a>
                    <a href="{{ route('admin.control-center') }}" class="nav-item {{ request()->routeIs('admin.control-center') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <span>Centro de Control</span>
                    </a>
                @endif
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <img src="{{ auth()->user()->avatar_url }}" style="width:40px; height:40px; border-radius:12px; border: 2px solid rgba(255,255,255,0.1); object-fit: cover;">
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name ?? 'Usuario' }}</div>
                    <div class="user-role">{{ auth()->user()->admin ? 'Administrador' : 'Fisioterapeuta' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin-top:8px;">
                @csrf
                <button type="submit" class="btn-modern btn-secondary btn-sm" style="width:100%;justify-content:center;">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="main-content">
        <div class="topbar">
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-actions">
                <button class="btn-icon" id="toggle-sidebar" style="display:none;">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <div class="content-area">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert-modern alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-modern alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-modern alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @livewireScripts
    @yield('js')

    <script>
        // Mobile sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('toggle-sidebar');
        if (window.innerWidth <= 768) {
            toggle.style.display = 'flex';
        }
        toggle?.addEventListener('click', () => sidebar.classList.toggle('open'));
    </script>
</body>
</html>
