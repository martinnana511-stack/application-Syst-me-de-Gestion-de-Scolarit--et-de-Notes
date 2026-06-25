<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SGS') — École Primaire</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --sgs-primary:   #1a5276;
            --sgs-accent:    #e67e22;
            --sgs-success:   #1e8449;
            --sgs-danger:    #c0392b;
            --sgs-bg:        #f4f6f9;
            --sgs-sidebar:   #0f3460;
            --sgs-sidebar-w: 250px;
            --font-body:     'DM Sans', sans-serif;
            --font-heading:  'Sora', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            background: var(--sgs-bg);
            color: #1c2833;
            min-height: 100vh;
        }

        /* ── Sidebar ───────────────────────────────── */
        #sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sgs-sidebar-w);
            background: var(--sgs-sidebar);
            display: flex; flex-direction: column;
            z-index: 1040;
            transition: transform .25s ease;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand h1 {
            font-family: var(--font-heading);
            font-size: 1.1rem;
            color: #fff;
            margin: 0;
            line-height: 1.3;
        }
        .sidebar-brand span {
            font-size: .7rem;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--sgs-accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-bottom: .6rem;
        }

        .nav-section {
            padding: .75rem 1rem .25rem;
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: rgba(255,255,255,.35);
        }

        .sidebar-nav .nav-link {
            display: flex; align-items: center; gap: .65rem;
            padding: .55rem 1.5rem;
            color: rgba(255,255,255,.7);
            font-size: .875rem;
            border-left: 3px solid transparent;
            transition: all .15s;
            border-radius: 0;
        }
        .sidebar-nav {
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            width: 100%;
        }
        .sidebar-nav.nav {
            flex-wrap: nowrap !important;
            flex-direction: column !important;
            width: var(--sgs-sidebar-w) !important;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.2);
            border-radius: 999px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,.4);
        }
        .sidebar-nav .nav-link i { font-size: 1rem; }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.08);
            border-left-color: var(--sgs-accent);
        }
        .sidebar-nav .nav-link.active { font-weight: 600; }

        .sidebar-user {
            margin-top: auto;
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; gap: .75rem;
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--sgs-accent);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: #fff; font-size: .85rem;
            flex-shrink: 0;
        }
        .sidebar-user .info small { color: rgba(255,255,255,.45); font-size: .7rem; display: block; }
        .sidebar-user .info strong { color: #fff; font-size: .8rem; }
        .sidebar-user form button {
            background: none; border: none; color: rgba(255,255,255,.45);
            padding: 0; margin-left: auto; font-size: 1.1rem; cursor: pointer;
        }
        .sidebar-user form button:hover { color: var(--sgs-danger); }

        /* ── Topbar ────────────────────────────────── */
        #topbar {
            position: fixed; top: 0; left: var(--sgs-sidebar-w); right: 0;
            height: 60px;
            background: #fff;
            border-bottom: 1px solid #e8ecf0;
            display: flex; align-items: center; padding: 0 1.75rem;
            z-index: 1030;
            gap: 1rem;
        }
        .topbar-title {
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 700;
            color: var(--sgs-primary);
            margin: 0;
        }
        .topbar-breadcrumb { font-size: .78rem; color: #7f8c8d; }
        .topbar-breadcrumb a { color: #7f8c8d; text-decoration: none; }
        .topbar-breadcrumb a:hover { color: var(--sgs-primary); }

        /* ── Content ───────────────────────────────── */
        #main-content {
            margin-left: var(--sgs-sidebar-w);
            padding-top: 60px;
            min-height: 100vh;
        }
        .page-body { padding: 1.75rem; }

        /* ── Cards ─────────────────────────────────── */
        .card {
            border: 1px solid #e4e8ed;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e4e8ed;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem;
            font-family: var(--font-heading);
            font-size: .95rem;
            font-weight: 600;
            color: var(--sgs-primary);
        }

        /* ── Stat cards ─────────────────────────────── */
        .stat-card {
            background: #fff;
            border: 1px solid #e4e8ed;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .stat-card .stat-icon {
            width: 48px; height: 48px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; flex-shrink: 0;
        }
        .stat-card .stat-value {
            font-family: var(--font-heading);
            font-size: 1.5rem; font-weight: 700;
            color: var(--sgs-primary); line-height: 1;
        }
        .stat-card .stat-label { font-size: .78rem; color: #7f8c8d; margin-top: .2rem; }

        /* ── Table ──────────────────────────────────── */
        .table th {
            font-size: .72rem; text-transform: uppercase;
            letter-spacing: .07em; color: #7f8c8d;
            font-weight: 600; border-bottom: 2px solid #e4e8ed;
            padding: .75rem 1rem;
        }
        .table td { padding: .75rem 1rem; vertical-align: middle; font-size: .875rem; }
        .table tbody tr:hover { background: #f8fafc; }

        /* ── Badges ─────────────────────────────────── */
        .badge-niveau {
            font-size: .7rem; padding: .25rem .6rem;
            border-radius: 6px; font-weight: 600;
        }

        /* ── Buttons ────────────────────────────────── */
        .btn-primary { background: var(--sgs-primary); border-color: var(--sgs-primary); }
        .btn-primary:hover { background: #154360; border-color: #154360; }
        .btn-accent { background: var(--sgs-accent); border-color: var(--sgs-accent); color: #fff; }
        .btn-accent:hover { background: #ca6f1e; border-color: #ca6f1e; color: #fff; }

        /* ── Alerts ─────────────────────────────────── */
        .alert { border-radius: 10px; border: none; font-size: .875rem; }

        /* ── Toasts ─────────────────────────────────── */
        .toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; }

        /* ── Progress ───────────────────────────────── */
        .progress { border-radius: 999px; }

        /* ── Responsive ─────────────────────────────── */
        @media (max-width: 991px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar, #main-content { left: 0; margin-left: 0; }
            #topbar { left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ═══════════════════ SIDEBAR ═══════════════════ --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">🏫</div>
        <h1>École Primaire</h1>
        <span><Command>Complexe Scolaire de Saaba</Command></span>
    </div>

    <ul class="sidebar-nav nav flex-column mt-2">

        @gestionnaire
        <li><span class="nav-section">Tableau de bord</span></li>
        <li class="nav-item">
            <a href="{{ route('dashboard.index') }}"
               class="nav-link @if(request()->routeIs('dashboard.*')) active @endif">
                <i class="bi bi-grid-1x2"></i> Vue d'ensemble
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('dashboard.impayes') }}"
               class="nav-link @if(request()->routeIs('dashboard.impayes')) active @endif">
                <i class="bi bi-exclamation-triangle"></i> Impayés
            </a>
        </li>
        @endgestionnaire

        <li><span class="nav-section">Scolarité</span></li>

        @gestionnaire
        <li class="nav-item">
            <a href="{{ route('students.index') }}"
               class="nav-link @if(request()->routeIs('students.*')) active @endif">
                <i class="bi bi-people"></i> Élèves
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('payments.index') }}"
               class="nav-link @if(request()->routeIs('payments.*')) active @endif">
                <i class="bi bi-cash-coin"></i> Paiements
            </a>
        </li>
        @endgestionnaire

        <li class="nav-item">
            <a href="{{ route('grades.index') }}"
               class="nav-link @if(request()->routeIs('grades.*')) active @endif">
                <i class="bi bi-pencil-square"></i> Notes
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('report-cards.index') }}"
               class="nav-link @if(request()->routeIs('report-cards.*')) active @endif">
                <i class="bi bi-file-earmark-text"></i> Bulletins
            </a>
        </li>

        @gestionnaire
        <li><span class="nav-section">Paramètres</span></li>
        <li class="nav-item">
            <a href="{{ route('settings.classes.index') }}"
               class="nav-link @if(request()->routeIs('settings.classes.*')) active @endif">
                <i class="bi bi-building"></i> Classes
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('settings.subjects.index') }}"
               class="nav-link @if(request()->routeIs('settings.subjects.*')) active @endif">
                <i class="bi bi-journal-bookmark"></i> Matières
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('settings.users.index') }}"
               class="nav-link @if(request()->routeIs('settings.users.*')) active @endif">
                <i class="bi bi-person-gear"></i> Utilisateurs
            </a>
        </li>
        <li class="nav-item">
    <a href="{{ route('settings.academic-years.index') }}"
       class="nav-link @if(request()->routeIs('settings.academic-years.*')) active @endif">
        <i class="bi bi-calendar3"></i> Années scolaires
    </a>
</li>

        <li><span class="nav-section">Communauté</span></li>
        <li class="nav-item">
            <a href="{{ route('parents.index') }}"
               class="nav-link @if(request()->routeIs('parents.*')) active @endif">
                <i class="bi bi-people-fill"></i> Parents
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('absences.index') }}"
               class="nav-link @if(request()->routeIs('absences.*')) active @endif">
                <i class="bi bi-calendar-x"></i> Absences
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('annonces.index') }}"
               class="nav-link @if(request()->routeIs('annonces.*')) active @endif">
                <i class="bi bi-megaphone"></i> Annonces
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('notifications.index') }}"
               class="nav-link @if(request()->routeIs('notifications.*')) active @endif">
                <i class="bi bi-bell"></i> Notifications
            </a>
        </li>
        @endgestionnaire

    </ul>

    <div class="sidebar-user">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
        <div class="info">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ ucfirst(auth()->user()->role) }}</small>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Déconnexion"><i class="bi bi-box-arrow-right"></i></button>
        </form>
    </div>
</nav>

{{-- ═══════════════════ TOPBAR ═══════════════════ --}}
<header id="topbar">
    <button class="btn btn-sm d-lg-none me-2" onclick="document.getElementById('sidebar').classList.toggle('show')">
        <i class="bi bi-list fs-5"></i>
    </button>
    <div>
        <p class="topbar-title mb-0">@yield('page-title', 'Tableau de bord')</p>
        <nav class="topbar-breadcrumb">
            <a href="{{ route('dashboard.index') }}">Accueil</a>
            @yield('breadcrumb')
        </nav>
    </div>
    <div class="ms-auto d-flex align-items-center gap-3">
        @if($year = \App\Models\AcademicYear::active())
            <span class="badge" style="background:#eaf2ff;color:var(--sgs-primary);font-size:.75rem;padding:.4rem .8rem;border-radius:8px;">
                <i class="bi bi-calendar3 me-1"></i>{{ $year->libelle }}
            </span>
        @endif
    </div>
</header>

{{-- ═══════════════════ MAIN ═══════════════════ --}}
<main id="main-content">
    <div class="page-body">

        {{-- Alertes flash --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <strong>Erreur :</strong> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@stack('scripts')
</body>
</html>
