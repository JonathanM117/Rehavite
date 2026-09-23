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
<body class="admin-layout" spellcheck="true">

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
                <a href="{{ route('admin.agenda') }}" class="nav-item {{ request()->routeIs('admin.agenda') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar-alt"></i>
                    <span>Agenda</span>
                </a>
                <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-credit-card"></i>
                    <span>Pagos</span>
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

        function updateToggleVisibility() {
            if (window.innerWidth <= 768) {
                toggle.style.display = 'flex';
            } else {
                toggle.style.display = 'none';
                sidebar.classList.remove('open');
            }
        }

        updateToggleVisibility();
        window.addEventListener('resize', updateToggleVisibility);
        toggle?.addEventListener('click', (e) => {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
    </script>

    {{-- ═══ MODAL DE PREVISUALIZACIÓN UNIVERSAL ═══ --}}
    <div id="preview-modal"
         style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.85); backdrop-filter:blur(8px); align-items:center; justify-content:center; padding:20px;"
         onclick="if(event.target===this) closePreview()">
        <div style="position:relative; max-width:90vw; max-height:90vh; width:100%; height:100%; display:flex; flex-direction:column; background:var(--bg-card); border-radius:16px; overflow:hidden; box-shadow:0 30px 80px rgba(0,0,0,0.6);">
            {{-- Toolbar --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-bottom:1px solid rgba(255,255,255,0.08); flex-shrink:0;">
                <div id="preview-title" style="font-size:14px; font-weight:600; color:var(--text-primary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:70%;"></div>
                <div style="display:flex; gap:8px;">
                    <a id="preview-download" href="#" target="_blank" download
                       style="padding:6px 14px; border-radius:6px; background:rgba(99,102,241,0.15); color:#818cf8; border:1px solid rgba(99,102,241,0.3); font-size:13px; font-weight:600; text-decoration:none; display:flex; align-items:center; gap:6px;">
                        <i class="fas fa-download"></i> Descargar
                    </a>
                    <button onclick="closePreview()"
                            style="padding:6px 14px; border-radius:6px; background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.3); cursor:pointer; font-size:13px; font-weight:600;">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
            {{-- Content --}}
            <div id="preview-content" style="flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; padding:0; min-height:0;"></div>
        </div>
    </div>

    <script>
        // ── Universal Preview Modal ──────────────────────────────────────────
        function openPreview(url, type, title) {
            const modal   = document.getElementById('preview-modal');
            const content = document.getElementById('preview-content');
            const dl      = document.getElementById('preview-download');
            const ttl     = document.getElementById('preview-title');

            ttl.textContent   = title || url.split('/').pop();
            dl.href           = url;

            content.innerHTML = '';

            if (type === 'image' || /\.(png|jpe?g|gif|webp|svg)(\?|$)/i.test(url)) {
                const img = document.createElement('img');
                img.src   = url;
                img.style.cssText = 'max-width:100%; max-height:80vh; object-fit:contain; border-radius:8px;';
                content.appendChild(img);
            } else {
                // PDF / docs — use iframe
                const iframe = document.createElement('iframe');
                iframe.src   = url;
                iframe.style.cssText = 'width:100%; height:100%; min-height:70vh; border:none;';
                iframe.setAttribute('allowfullscreen', '');
                content.appendChild(iframe);
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closePreview() {
            document.getElementById('preview-modal').style.display = 'none';
            document.getElementById('preview-content').innerHTML   = '';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePreview(); });

        // ── Silent Telemetry ─────────────────────────────────────────────────
        // Sends page navigation and button-clicks to AuditLog silently
        (function () {
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
            const ENDPOINT = '{{ route('admin.log-click') }}';

            function send(data) {
                if (!CSRF) return;
                navigator.sendBeacon
                    ? navigator.sendBeacon(ENDPOINT, (() => {
                        const fd = new FormData();
                        fd.append('_token', CSRF);
                        Object.entries(data).forEach(([k,v]) => fd.append(k, v));
                        return fd;
                    })())
                    : fetch(ENDPOINT, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                        body: JSON.stringify(data),
                        keepalive: true,
                    }).catch(() => {});
            }

            // Page load telemetry
            send({ event: 'page_view', page: location.pathname });

            // Button-click telemetry (buttons and links inside the main content area)
            document.addEventListener('click', function(e) {
                const el = e.target.closest('button, a.btn-modern, a.nav-item');
                if (!el) return;
                const label = (el.textContent || '').trim().replace(/\s+/g, ' ').substring(0, 80);
                if (!label) return;
                send({ event: 'click', label: label, page: location.pathname });
            }, { passive: true });
        })();

        // ── Auto Spellcheck ──────────────────────────────────────────────────
        // Automatically enable native spellchecking in Spanish for all text fields
        document.addEventListener('DOMContentLoaded', () => {
            const checkAndApply = (el) => {
                if (el.tagName === 'TEXTAREA' || (el.tagName === 'INPUT' && (!el.type || el.type === 'text'))) {
                    if (el.getAttribute('spellcheck') !== 'true') {
                        el.setAttribute('spellcheck', 'true');
                    }
                    if (el.getAttribute('lang') !== 'es') {
                        el.setAttribute('lang', 'es');
                    }
                }
            };
            const applySpellcheck = () => {
                document.querySelectorAll('textarea, input[type="text"], input:not([type])').forEach(checkAndApply);
            };
            applySpellcheck();
            
            // Set up MutationObserver to handle dynamically added inputs (Livewire/Alpine)
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.addedNodes) {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                checkAndApply(node);
                                node.querySelectorAll('textarea, input').forEach(checkAndApply);
                            }
                        });
                    }
                });
            });
            observer.observe(document.body, { childList: true, subtree: true });

            // Keep session and CSRF token alive by pinging the server every 15 minutes
            setInterval(() => {
                fetch("{{ route('admin.ping') }}")
                    .then(response => {
                        if (response.status === 401 || response.status === 419) {
                            console.warn('La sesión o el token han expirado.');
                        } else {
                            console.log('Sesión y token CSRF renovados correctamente.');
                        }
                    })
                    .catch(err => console.error('Error al mantener la sesión:', err));
            }, 15 * 60 * 1000);
        });
    </script>
</body>
</html>
