<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Applied before first paint so there is no flash of the wrong theme or accent.
        (function () {
            var savedTheme = localStorage.getItem('cms-theme') || 'light';
            var savedAccent = localStorage.getItem('cms-accent') || 'purple';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-cms-accent', savedAccent);
        })();
    </script>
    <title>{{ $siteSettings->site_name ?? 'CMS - College Management System' }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Choices.js (searchable select / multi-select) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css">
    <!-- CMS Professional Theme & Animations -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <style>
        :root {
            /* Harivandana College brand palette — purple-to-blue from the logo wordmark,
               with the swirl's warm accent colors reserved for the top brand strip. */
            --cms-primary: #6d3fa0;
            --cms-primary-dark: #4a1f6e;
            --cms-secondary: #2e5fa3;
            --cms-dark: #2a1240;
            --cms-light: #f8fafc;
            --cms-card-bg: #ffffff;
            --cms-border: #e2e8f0;

            --cms-accent-orange: #f5841f;
            --cms-accent-pink: #e6007e;
            --cms-accent-teal: #2ba84a;
            --cms-accent-yellow: #f5c400;

            /* Re-theme Bootstrap's "primary" to the brand purple everywhere it's used
               (buttons, badges, text-primary, bg/border-subtle) without touching every view. */
            --bs-primary: #6d3fa0;
            --bs-primary-rgb: 109, 63, 160;
            --bs-primary-text-emphasis: #46295f;
            --bs-primary-bg-subtle: #ede4f7;
            --bs-primary-border-subtle: #d2beea;
            --bs-link-color: #6d3fa0;
            --bs-link-color-rgb: 109, 63, 160;
            --bs-link-hover-color: #4a1f6e;
            --bs-link-hover-color-rgb: 74, 31, 110;
        }

        .btn-primary {
            --bs-btn-bg: #6d3fa0;
            --bs-btn-border-color: #6d3fa0;
            --bs-btn-hover-bg: #5c3488;
            --bs-btn-hover-border-color: #562f7e;
            --bs-btn-active-bg: #562f7e;
            --bs-btn-active-border-color: #502c75;
            --bs-btn-disabled-bg: #6d3fa0;
            --bs-btn-disabled-border-color: #6d3fa0;
            --bs-btn-focus-shadow-rgb: 138, 106, 179;
        }

        .btn-outline-primary {
            --bs-btn-color: #6d3fa0;
            --bs-btn-border-color: #6d3fa0;
            --bs-btn-hover-bg: #6d3fa0;
            --bs-btn-hover-border-color: #6d3fa0;
            --bs-btn-active-bg: #6d3fa0;
            --bs-btn-active-border-color: #6d3fa0;
            --bs-btn-disabled-color: #6d3fa0;
            --bs-btn-disabled-border-color: #6d3fa0;
            --bs-btn-focus-shadow-rgb: 138, 106, 179;
        }

        .text-primary { color: #6d3fa0 !important; }
        .badge.bg-primary, .bg-primary { background-color: #6d3fa0 !important; }
        .bg-primary-subtle { background-color: #ede4f7 !important; }
        .border-primary-subtle { border-color: #d2beea !important; }
        .text-primary-emphasis { color: #46295f !important; }

        html {
            font-size: 18px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
        }

        /* Brand strip — echoes the logo's colorful swirl */
        .brand-strip {
            height: 5px;
            background: linear-gradient(90deg,
                var(--cms-accent-orange) 0%,
                var(--cms-accent-pink) 25%,
                var(--cms-primary) 50%,
                var(--cms-secondary) 75%,
                var(--cms-accent-teal) 100%);
        }

        .navbar-custom {
            background: linear-gradient(135deg, var(--cms-primary-dark) 0%, var(--cms-secondary) 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .sidebar {
            background: #ffffff;
            border-right: 1px solid var(--cms-border);
            min-height: calc(100vh - 70px);
        }

        .sidebar .nav-link {
            color: #64748b;
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            margin: 0.2rem 0.75rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #ede4f7;
            color: var(--cms-primary);
        }

        .sidebar .nav-link.active {
            font-weight: 700;
            box-shadow: inset 3px 0 0 var(--cms-primary);
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.1rem;
        }

        .nav-section-label {
            font-size: 15px !important;
            font-weight: 800 !important;
            letter-spacing: 0.04em;
            color: #64748b !important;
        }

        .nav-group-header {
            cursor: pointer;
            user-select: none;
        }
        .nav-group-header:hover { color: var(--cms-primary) !important; }
        .nav-group-header .fa-chevron-down {
            font-size: 10px;
            transition: transform 0.2s ease;
        }
        .nav-group-header[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }

        .stat-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid var(--cms-border);
            padding: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .badge-role-management { background: #f43f5e; color: #fff; }
        .badge-role-staff { background: var(--cms-secondary); color: #fff; }
        .badge-role-student { background: var(--cms-accent-teal); color: #fff; }

        .theme-toggle-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .theme-toggle-btn:hover { background: rgba(255,255,255,0.2); color: #fff; }

        /* ---------------- Professional polish pass ---------------- */

        ::-webkit-scrollbar { width: 9px; height: 9px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d2beea; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--cms-primary); }
        * { scrollbar-width: thin; scrollbar-color: #d2beea transparent; }

        h1, h2, h3, h4, h5, h6 { letter-spacing: -0.01em; }

        .navbar-custom { border-bottom: 1px solid rgba(255,255,255,0.08); }
        .navbar-brand span { letter-spacing: -0.01em; }

        /* Cards: one consistent depth/radius language app-wide */
        .card {
            border-radius: 1rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 6px rgba(15, 23, 42, 0.04);
            transition: box-shadow 0.2s ease;
        }
        .card-header {
            font-weight: 700;
        }

        /* Buttons: subtle lift + brand-colored shadow on the primary actions */
        .btn {
            border-radius: 0.65rem;
            font-weight: 600;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        .btn-sm { border-radius: 0.5rem; }
        .btn-primary, .btn-success, .btn-danger {
            box-shadow: 0 2px 8px rgba(109, 63, 160, 0.18);
        }
        .btn-primary:hover, .btn-success:hover, .btn-danger:hover {
            transform: translateY(-1px);
        }
        .btn:active { transform: translateY(0); }
        .btn-check:checked + .btn, .btn.active { box-shadow: none; }

        /* Tables: quieter, more legible headers + a softer hover than Bootstrap's default */
        .table thead th {
            text-transform: uppercase;
            font-size: 0.68rem;
            letter-spacing: 0.05em;
            font-weight: 800;
            color: #64748b;
            border-bottom-width: 2px;
        }
        .table-hover > tbody > tr:hover > * {
            background-color: rgba(109, 63, 160, 0.045);
        }

        .badge { font-weight: 600; letter-spacing: 0.01em; }

        /* Forms: brand-colored focus ring instead of Bootstrap's default blue */
        .form-control:focus, .form-select:focus {
            border-color: var(--cms-primary);
            box-shadow: 0 0 0 0.2rem rgba(109, 63, 160, 0.15);
        }

        /* Modals: softer, more premium elevation than Bootstrap's flat default */
        .modal-content {
            border: none;
            border-radius: 1.1rem;
            box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.35);
        }
        .modal-header, .modal-footer { border-color: var(--cms-border); }

        /* ---------------- Dark theme high-contrast overrides ---------------- */
        [data-bs-theme="dark"] {
            --bs-primary-bg-subtle: #3a2a52;
            --bs-primary-border-subtle: #5c3f82;
            --bs-primary-text-emphasis: #d8c2ef;
        }
        [data-bs-theme="dark"] body {
            background-color: #0f0f1a;
            color: #f1f5f9;
        }
        [data-bs-theme="dark"] .sidebar {
            background: #16161f;
            border-right-color: #2a2a3a;
        }
        [data-bs-theme="dark"] .sidebar .nav-link {
            color: #cbd5e1;
        }
        [data-bs-theme="dark"] .sidebar .nav-link:hover,
        [data-bs-theme="dark"] .sidebar .nav-link.active {
            background: #2a2043;
            color: #c9a8e8;
        }
        [data-bs-theme="dark"] .nav-section-label {
            color: #94a3b8 !important;
        }
        [data-bs-theme="dark"] .nav-group-header:hover {
            color: #c9a8e8 !important;
        }
        [data-bs-theme="dark"] .stat-card {
            background: #1a1a26;
            border-color: #2a2a3a;
        }
        [data-bs-theme="dark"] .bg-white {
            background-color: #1a1a26 !important;
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .bg-light,
        [data-bs-theme="dark"] .table-light,
        [data-bs-theme="dark"] .table-light th {
            background-color: #14141f !important;
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .table {
            --bs-table-color: #f1f5f9;
        }
        [data-bs-theme="dark"] .text-dark,
        [data-bs-theme="dark"] .text-black {
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .text-muted,
        [data-bs-theme="dark"] .text-secondary {
            color: #cbd5e1 !important;
        }
        [data-bs-theme="dark"] i.text-dark,
        [data-bs-theme="dark"] i.text-secondary,
        [data-bs-theme="dark"] i.text-muted {
            color: #e2e8f0 !important;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #14141f;
            border-color: #3e3e56;
            color: #f1f5f9;
        }
        [data-bs-theme="dark"] .modal-content {
            background-color: #1a1a26;
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.6);
        }
        [data-bs-theme="dark"] .modal-header,
        [data-bs-theme="dark"] .modal-footer {
            border-color: #2a2a3a;
        }
        [data-bs-theme="dark"] .card {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2), 0 1px 6px rgba(0, 0, 0, 0.2);
            background-color: #1a1a26;
            border-color: #2a2a3a;
        }
        [data-bs-theme="dark"] .card-header {
            border-color: #2a2a3a !important;
            color: #f1f5f9 !important;
        }
        [data-bs-theme="dark"] .table thead th {
            color: #cbd5e1;
        }
        [data-bs-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: rgba(201, 168, 232, 0.08);
            color: #ffffff;
        }
        [data-bs-theme="dark"] ::-webkit-scrollbar-thumb {
            background: #3a2a52;
        }
        [data-bs-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background: #5c3f82;
        }
        [data-bs-theme="dark"] * {
            scrollbar-color: #3a2a52 transparent;
        }

        @media print {
            .navbar-custom, .sidebar, .brand-strip, .btn-print, .modal-backdrop, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .main-content {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/campus.css') }}?v={{ filemtime(public_path('css/campus.css')) }}">
    @if(request()->routeIs('dashboard'))
        <link rel="stylesheet" href="{{ asset('css/harivandana-preview.css') }}?v={{ filemtime(public_path('css/harivandana-preview.css')) }}">
    @endif
</head>
<body>
    <a href="#main-content" class="cms-skip-link">{{ __('public.skip_to_content') }}</a>
    <div class="brand-strip no-print"></div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top no-print" aria-label="Main navigation">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white fs-4" href="{{ route('dashboard') }}">
                @if(request()->routeIs('dashboard'))
                    <img class="harivandana-logo" src="{{ asset('images/harivandana-logo.png') }}" alt="Harivandana College — affiliated to Saurashtra University">
                @elseif(!empty($siteSettings?->logo_url))
                    <img src="{{ $siteSettings->logo_url }}" alt="Logo" height="32" class="rounded">
                @else
                    <i class="fa-solid fa-graduation-cap text-warning fs-3"></i>
                @endif
                @unless(request()->routeIs('dashboard'))
                    <span>{{ $siteSettings->site_name ?? 'CMS College of Technology' }}</span>
                @endunless
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                    aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="ms-auto d-flex align-items-center gap-2">
                    @auth
                        @php $unreadAlerts = Auth::user()->unreadAlertCount(); @endphp
                        <a href="{{ route('alerts.index') }}" class="btn btn-sm btn-outline-light position-relative rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Notifications" aria-label="Notifications{{ $unreadAlerts ? ", {$unreadAlerts} unread" : '' }}">
                            <i class="fa-solid fa-bell fs-6" aria-hidden="true"></i>
                            @if($unreadAlerts)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.45em;">{{ $unreadAlerts }}</span>
                            @endif
                        </a>
                    @endauth

                    <button type="button" class="theme-toggle-btn" onclick="cmsToggleTheme()" title="Toggle dark mode" aria-label="Toggle dark mode">
                        <i id="themeToggleIcon" class="fa-solid fa-moon"></i>
                    </button>

                    @auth
                        <!-- Three-Dot Profile & Action Menu -->
                        <div class="dropdown">
                            <button class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center p-0 shadow-sm" type="button" id="cmsHeaderThreeDotMenu" data-bs-toggle="dropdown" aria-expanded="false" style="width: 38px; height: 38px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);" title="Account & Actions">
                                <i class="fa-solid fa-ellipsis-vertical fs-5 text-white"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" aria-labelledby="cmsHeaderThreeDotMenu" style="min-width: 270px; border-radius: 0.85rem;">
                                <!-- User Profile Card -->
                                <li class="px-3 py-2 bg-body-tertiary rounded-3 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem; flex-shrink: 0;">
                                            @if(Auth::user()->profile_photo_path ?? false)
                                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" alt="{{ Auth::user()->name }}" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                            @else
                                                <i class="fa-solid fa-user"></i>
                                            @endif
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-truncate text-body" style="max-width: 180px;">{{ Auth::user()->name }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 180px; font-size: 0.8rem;">{{ Auth::user()->email }}</div>
                                            <span class="badge rounded-pill badge-role-{{ Auth::user()->role }} text-uppercase px-2 py-0-5 fs-7 mt-1">
                                                {{ Auth::user()->role }}
                                            </span>
                                        </div>
                                    </div>
                                </li>

                                <!-- Menu Items -->
                                @if(Auth::user()->isManagement())
                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2 text-warning fw-semibold" data-bs-toggle="modal" data-bs-target="#cmsAdminBroadcastModal">
                                        <i class="fa-solid fa-tower-broadcast text-danger fa-fw"></i>
                                        <span>Broadcast Live</span>
                                    </button>
                                </li>
                                @endif

                                <li>
                                    <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2" data-bs-toggle="modal" data-bs-target="#cmsSystemGuideModal">
                                        <i class="fa-solid fa-circle-question text-warning fa-fw"></i>
                                        <span>System Guide</span>
                                    </button>
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2" href="{{ route('alerts.index') }}">
                                        <i class="fa-solid fa-bell text-primary fa-fw"></i>
                                        <span>Notifications</span>
                                        @if(!empty($unreadAlerts))
                                            <span class="badge bg-danger ms-auto rounded-pill">{{ $unreadAlerts }}</span>
                                        @endif
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider my-2"></li>

                                <!-- Logout Action -->
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 rounded-2 text-danger fw-bold">
                                            <i class="fa-solid fa-right-from-bracket fa-fw"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="campus-mobile-nav d-md-none no-print">
        <button class="btn btn-outline-primary w-100 text-start" type="button" data-bs-toggle="collapse" data-bs-target="#campusSidebar" aria-expanded="false" aria-controls="campusSidebar">
            <i class="fa-solid fa-bars me-2" aria-hidden="true"></i> Explore your workspace
        </button>
    </div>
    <div class="container-fluid cms-shell">
        <div class="row">
            <!-- Sidebar -->
            <aside id="campusSidebar" class="col-md-3 col-lg-2 sidebar p-0 no-print collapse d-md-block" aria-label="Sidebar navigation">
                <div class="py-3">
                    <div class="nav-section-label px-3 mb-3 text-uppercase" id="nav-heading"><i class="fa-solid fa-compass text-primary me-2"></i> Navigation Menu</div>
                    <nav class="nav flex-column" aria-labelledby="nav-heading">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-gauge-high text-primary me-2"></i> Dashboard
                        </a>

                        @if(Auth::user()->isManagement())
                        @php $academicSetupOpen = request()->routeIs(['academics.*', 'sections.*']); @endphp
                        <button type="button" class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupAcademicSetup" role="button" aria-expanded="{{ $academicSetupOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-layer-group text-primary me-2"></i> Academic Setup</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </button>
                        <div class="collapse {{ $academicSetupOpen ? 'show' : '' }}" id="groupAcademicSetup">
                            <a class="nav-link {{ request()->routeIs('academics.departments') ? 'active' : '' }}" href="{{ route('academics.departments') }}">
                                <i class="fa-solid fa-building-columns text-info me-2"></i> Departments
                            </a>
                            <a class="nav-link {{ request()->routeIs('academics.courses') ? 'active' : '' }}" href="{{ route('academics.courses') }}">
                                <i class="fa-solid fa-graduation-cap text-warning me-2"></i> Degree Courses
                            </a>
                            <a class="nav-link {{ request()->routeIs('academics.subjects') ? 'active' : '' }}" href="{{ route('academics.subjects') }}">
                                <i class="fa-solid fa-book-open text-primary me-2"></i> Subjects & Curriculum
                            </a>
                            <a class="nav-link {{ request()->routeIs('academics.staff') ? 'active' : '' }}" href="{{ route('academics.staff') }}">
                                <i class="fa-solid fa-user-tie text-success me-2"></i> Faculty & Teachers
                            </a>
                            <a class="nav-link {{ request()->routeIs('sections.*') ? 'active' : '' }}" href="{{ route('sections.index') }}">
                                <i class="fa-solid fa-chalkboard-user me-2" style="color: #8b5cf6;"></i> Class Sections
                            </a>
                        </div>
                        @endif

                        @php
                            $studentGroupRoutes = ['admissions.*', 'admission-applications.*', 'certificates.*', 'fees.*', 'attendance.*'];
                            $studentGroupOpen = request()->routeIs($studentGroupRoutes);
                        @endphp
                        <button type="button" class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupStudent" role="button" aria-expanded="{{ $studentGroupOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-user-graduate text-success me-2"></i> Student Management</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </button>
                        <div class="collapse {{ $studentGroupOpen ? 'show' : '' }}" id="groupStudent">
                            @if(!Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}" href="{{ route('admissions.index') }}">
                                <i class="fa-solid fa-address-book text-primary me-2"></i> Student Directory
                            </a>
                            @endif

                            @if(Auth::user()->isManagement())
                            <a class="nav-link {{ request()->routeIs('admissions.import*') ? 'active' : '' }}" href="{{ route('admissions.import') }}">
                                <i class="fa-solid fa-file-csv text-success me-2"></i> Bulk Import
                            </a>
                            <a class="nav-link {{ request()->routeIs('admission-applications.*') ? 'active' : '' }}" href="{{ route('admission-applications.index') }}">
                                <i class="fa-solid fa-file-signature text-warning me-2"></i> Admission Applications
                                @php $pendingAppsCount = \App\Models\AdmissionApplication::where('status', 'Pending')->count(); @endphp
                                @if($pendingAppsCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-1">{{ $pendingAppsCount }}</span>
                                @endif
                            </a>
                            @endif

                            <a class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}" href="{{ route('certificates.index') }}">
                                <i class="fa-solid fa-certificate text-danger me-2"></i> {{ Auth::user()->isStudent() ? 'My Certificates' : 'Certificates' }}
                            </a>

                            @if(Auth::user()->isManagement())
                            <a class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}" href="{{ route('fees.index') }}">
                                <i class="fa-solid fa-file-invoice-dollar text-success me-2"></i> Fees & Dues
                            </a>
                            @endif

                            @if(!Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                                <i class="fa-solid fa-clipboard-user text-info me-2"></i> Attendance Log
                            </a>
                            @endif
                        </div>

                        @php $academicsOpen = request()->routeIs(['exams.*', 'timetables.*', 'materials.*', 'academics.my_syllabus', 'academics.syllabi']); @endphp
                        <button type="button" class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupAcademics" role="button" aria-expanded="{{ $academicsOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-book-bookmark text-warning me-2"></i> Academics & Learning</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </button>
                        <div class="collapse {{ $academicsOpen ? 'show' : '' }}" id="groupAcademics">
                            <a class="nav-link {{ request()->routeIs('academics.my_syllabus') ? 'active' : '' }}" href="{{ route('academics.my_syllabus') }}">
                                <i class="fa-solid fa-scroll text-warning me-2"></i> Syllabus & Faculty
                            </a>
                            @if(!Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('academics.syllabi') ? 'active' : '' }}" href="{{ route('academics.syllabi') }}">
                                <i class="fa-solid fa-file-pdf text-danger me-2"></i> Semester Syllabi
                            </a>
                            @endif
                            <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">
                                <i class="fa-solid fa-award text-primary me-2"></i> Exam Marks & CGPA
                            </a>
                            @if(!Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('exams.collection.*') ? 'active' : '' }}" href="{{ route('exams.collection.index') }}">
                                <i class="fa-solid fa-hand-holding-hand text-info me-2"></i> Result Collection
                            </a>
                            @endif
                            <a class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}" href="{{ route('timetables.index') }}">
                                <i class="fa-solid fa-calendar-days text-success me-2"></i> Timetable & Calendar
                            </a>
                            <a class="nav-link {{ request()->routeIs('alerts.*') ? 'active' : '' }}" href="{{ route('alerts.index') }}">
                                <i class="fa-solid fa-bell text-warning me-2"></i> Notifications
                            </a>
                            <a class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}" href="{{ route('materials.index') }}">
                                <i class="fa-solid fa-folder-open text-primary me-2"></i> Materials & Tasks
                            </a>
                        </div>

                        @php $campusOpen = request()->routeIs(['leaves.*', 'complaints.*', 'notices.*']); @endphp
                        <button type="button" class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupCampus" role="button" aria-expanded="{{ $campusOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-building-user text-info me-2"></i> Campus Support</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </button>
                        <div class="collapse {{ $campusOpen ? 'show' : '' }}" id="groupCampus">
                            @unless(Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}" href="{{ route('leaves.index') }}">
                                <i class="fa-solid fa-calendar-check text-warning me-2"></i> Leave Management
                            </a>
                            @endunless
                            <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                                <i class="fa-solid fa-headset text-danger me-2"></i> Grievances & Support
                            </a>
                            <a class="nav-link {{ request()->routeIs('notices.*') ? 'active' : '' }}" href="{{ route('notices.index') }}">
                                <i class="fa-solid fa-note-sticky text-primary me-2"></i> Notice Board
                            </a>
                        </div>

                        @if(Auth::user()->isManagement())
                        @php $outreachOpen = request()->routeIs(['whatsapp.*', 'broadcasts.*', 'marketing.*', 'settings.*']); @endphp
                        <button type="button" class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupOutreach" role="button" aria-expanded="{{ $outreachOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-tower-broadcast text-danger me-2"></i> Outreach & Settings</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </button>
                        <div class="collapse {{ $outreachOpen ? 'show' : '' }}" id="groupOutreach">
                            <a class="nav-link {{ request()->routeIs('whatsapp.*') ? 'active' : '' }}" href="{{ route('whatsapp.index') }}">
                                <i class="fa-brands fa-whatsapp text-success me-2 fs-5"></i> WhatsApp Hub
                            </a>
                            <a class="nav-link {{ request()->routeIs('broadcasts.*') ? 'active' : '' }}" href="{{ route('broadcasts.index') }}">
                                <i class="fa-solid fa-satellite-dish text-warning me-2"></i> Real-Time Broadcasts
                            </a>
                            <a class="nav-link {{ request()->routeIs('marketing.*') ? 'active' : '' }}" href="{{ route('marketing.index') }}">
                                <i class="fa-solid fa-comment-sms text-primary me-2"></i> SMS & Marketing
                            </a>
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">
                                <i class="fa-solid fa-sliders text-secondary me-2"></i> Website Settings
                            </a>
                        </div>
                        @endif

                        <div class="px-3 pt-3 pb-2 border-top mt-3">
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-pill d-flex align-items-center justify-content-center gap-1 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#cmsSystemGuideModal">
                                <i class="fa-solid fa-compass text-warning"></i> System Guide & Directory
                            </button>
                        </div>
                    </nav>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main id="main-content" class="col-md-9 col-lg-10 p-4 main-content animate-fade-in">
                <div class="campus-context no-print">
                    <span>Campus workspace <span class="mx-2" aria-hidden="true">/</span> <strong>{{ Auth::user()->role }}</strong></span>
                    <time datetime="{{ now()->toDateString() }}">{{ now()->format('l, d M Y') }}</time>
                </div>
                <!-- Session Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- System Guide & Feature Role Directory Modal -->
    <div class="modal fade no-print" id="cmsSystemGuideModal" tabindex="-1" aria-labelledby="cmsSystemGuideModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #6d3fa0 0%, #2e5fa3 100%);">
                    <div>
                        <h4 class="modal-title fw-bold d-flex align-items-center gap-2" id="cmsSystemGuideModalLabel">
                            <i class="fa-solid fa-compass text-warning fs-3"></i> System Feature Guide & Module Directory
                        </h4>
                        <p class="mb-0 fs-7 text-white-50">Comprehensive overview of what each module does and where to perform your work based on your role.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Role Tabs Navigation -->
                    <ul class="nav nav-pills nav-fill gap-2 mb-4 p-2 bg-light rounded-3 border" id="guideTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-2 rounded-2" id="guide-admin-tab" data-bs-toggle="tab" data-bs-target="#guide-admin" type="button" role="tab">
                                <i class="fa-solid fa-user-shield me-1"></i> Admin / Management Guide
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2 rounded-2" id="guide-staff-tab" data-bs-toggle="tab" data-bs-target="#guide-staff" type="button" role="tab">
                                <i class="fa-solid fa-user-tie me-1"></i> Faculty & Teacher Guide
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2 rounded-2" id="guide-student-tab" data-bs-toggle="tab" data-bs-target="#guide-student" type="button" role="tab">
                                <i class="fa-solid fa-user-graduate me-1"></i> Student Guide
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="guideTabsContent">
                        <!-- 1. Admin / Management Guide -->
                        <div class="tab-pane fade show active" id="guide-admin" role="tabpanel">
                            <div class="alert alert-info border-0 rounded-3 mb-4">
                                <i class="fa-solid fa-circle-info me-2"></i> <strong>Management Role Overview:</strong> As an Administrator, you have full access to configure academic structures, manage faculty & student records, issue fees & certificates, send sitewide real-time broadcasts, and configure automated WhatsApp triggers.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-2 fs-5"><i class="fa-solid fa-layer-group"></i></div>
                                            <h6 class="fw-bold mb-0">Academic Setup</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Define college infrastructure including Departments, Degree Courses, Semester Curriculums, Class Sections, and Faculty Profile mappings.</p>
                                        <a href="{{ route('academics.departments') }}" class="btn btn-sm btn-outline-primary mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Go to Setup</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-success-subtle text-success rounded-3 p-2 fs-5"><i class="fa-solid fa-user-graduate"></i></div>
                                            <h6 class="fw-bold mb-0">Student Directory & Import</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Admit new students, perform bulk CSV imports, approve pending admission applications, and manage active student rosters.</p>
                                        <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-outline-success mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Student Directory</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-warning-subtle text-warning rounded-3 p-2 fs-5"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                            <h6 class="fw-bold mb-0">Fees, Dues & Receipts</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Configure stream fee structures, collect student installments, view pending dues ledgers, and auto-issue official tax payment receipts.</p>
                                        <a href="{{ route('fees.index') }}" class="btn btn-sm btn-outline-warning mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Fee Ledger</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-danger-subtle text-danger rounded-3 p-2 fs-5"><i class="fa-solid fa-tower-broadcast"></i></div>
                                            <h6 class="fw-bold mb-0">Real-Time Broadcasts</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Send instant popup messages (toast, modal, banner) live to all connected users in real time without requiring page reloads.</p>
                                        <a href="{{ route('broadcasts.index') }}" class="btn btn-sm btn-outline-danger mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Broadcasts</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-success-subtle text-success rounded-3 p-2 fs-5"><i class="fa-brands fa-whatsapp"></i></div>
                                            <h6 class="fw-bold mb-0">WhatsApp Hub</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Send single/bulk WhatsApp messages to students or parents, view delivery logs, and toggle automated WhatsApp event triggers.</p>
                                        <a href="{{ route('whatsapp.index') }}" class="btn btn-sm btn-outline-success mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> WhatsApp Hub</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-info-subtle text-info rounded-3 p-2 fs-5"><i class="fa-solid fa-sliders"></i></div>
                                            <h6 class="fw-bold mb-0">Website Settings</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Update institution branding, logo URL, address, contact phone/email, and Meta WhatsApp Cloud API credentials.</p>
                                        <a href="{{ route('settings.edit') }}" class="btn btn-sm btn-outline-info mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Settings</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Faculty & Teacher Guide -->
                        <div class="tab-pane fade" id="guide-staff" role="tabpanel">
                            <div class="alert alert-success border-0 rounded-3 mb-4">
                                <i class="fa-solid fa-circle-info me-2"></i> <strong>Faculty Role Overview:</strong> As a Teacher/Faculty member, your primary workflow revolves around managing assigned subjects, recording attendance, uploading syllabus PDFs, evaluating assignments, and submitting exam marks.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-warning-subtle text-warning rounded-3 p-2 fs-5"><i class="fa-solid fa-scroll"></i></div>
                                            <h6 class="fw-bold mb-0">Syllabus & Faculty Mapping</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Upload semester-wise syllabus PDF documents and view subject teaching assignments for your department.</p>
                                        <a href="{{ route('academics.my_syllabus') }}" class="btn btn-sm btn-outline-warning mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> View Syllabus</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-info-subtle text-info rounded-3 p-2 fs-5"><i class="fa-solid fa-clipboard-user"></i></div>
                                            <h6 class="fw-bold mb-0">Daily Attendance Log</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Record daily student attendance per subject. Marking a student absent automatically dispatches parent WhatsApp alerts if configured.</p>
                                        <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-outline-info mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Attendance Log</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-2 fs-5"><i class="fa-solid fa-award"></i></div>
                                            <h6 class="fw-bold mb-0">Exam Marks & Collection</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Input internal assessment and end-term examination marks. Automatically calculate CGPA, grades, and generate student marksheets.</p>
                                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-primary mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Exam Marks</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-2 fs-5"><i class="fa-solid fa-folder-open"></i></div>
                                            <h6 class="fw-bold mb-0">Study Materials & Homework</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Upload subject lecture notes, slides, and homework assignments. Review and grade online student task submissions.</p>
                                        <a href="{{ route('materials.index') }}" class="btn btn-sm btn-outline-primary mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Study Materials</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-success-subtle text-success rounded-3 p-2 fs-5"><i class="fa-solid fa-calendar-days"></i></div>
                                            <h6 class="fw-bold mb-0">Timetable & Schedule</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">View weekly lecture schedules, classroom allotments, and timetable slots for your allocated courses.</p>
                                        <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-outline-success mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Timetable</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-warning-subtle text-warning rounded-3 p-2 fs-5"><i class="fa-solid fa-calendar-check"></i></div>
                                            <h6 class="fw-bold mb-0">Faculty Leave Applications</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Submit staff leave requests (Medical, Casual, Duty) directly to management and track approval status.</p>
                                        <a href="{{ route('leaves.index') }}" class="btn btn-sm btn-outline-warning mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Leave Portal</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Student Guide -->
                        <div class="tab-pane fade" id="guide-student" role="tabpanel">
                            <div class="alert alert-warning border-0 text-dark rounded-3 mb-4">
                                <i class="fa-solid fa-circle-info me-2"></i> <strong>Student Role Overview:</strong> Welcome! As a Student, you can download your official Syllabus PDF, view class timetables, track exam marks & CGPA, download Bonafide Certificates with photo, and submit homework tasks online.
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-warning-subtle text-warning rounded-3 p-2 fs-5"><i class="fa-solid fa-scroll"></i></div>
                                            <h6 class="fw-bold mb-0">My Syllabus & Faculty</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Download your official enrolled course syllabus PDF and check which professors are teaching your current semester subjects.</p>
                                        <a href="{{ route('academics.my_syllabus') }}" class="btn btn-sm btn-outline-warning mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Download Syllabus</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-danger-subtle text-danger rounded-3 p-2 fs-5"><i class="fa-solid fa-certificate"></i></div>
                                            <h6 class="fw-bold mb-0">My Certificates & ID Card</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Generate official Bonafide Student Certificates (for Bus Pass, Scholarship, Passport) featuring your photo and printable Student ID Card.</p>
                                        <a href="{{ route('certificates.index') }}" class="btn btn-sm btn-outline-danger mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> My Certificates</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-2 fs-5"><i class="fa-solid fa-award"></i></div>
                                            <h6 class="fw-bold mb-0">Exam Marksheet & CGPA</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">View semester subject marks, internal evaluation breakdown, cumulative grade point average (CGPA), and download PDF marksheets.</p>
                                        <a href="{{ route('exams.index') }}" class="btn btn-sm btn-outline-primary mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> My Marksheet</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-success-subtle text-success rounded-3 p-2 fs-5"><i class="fa-solid fa-calendar-days"></i></div>
                                            <h6 class="fw-bold mb-0">Class Timetable</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Check your daily class lecture schedule, classroom numbers, and timings so you never miss a lecture.</p>
                                        <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-outline-success mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> View Timetable</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-primary-subtle text-primary rounded-3 p-2 fs-5"><i class="fa-solid fa-folder-open"></i></div>
                                            <h6 class="fw-bold mb-0">Study Materials & Tasks</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Download lecture slides and notes shared by your professors, and upload your homework assignment files before due dates.</p>
                                        <a href="{{ route('materials.index') }}" class="btn btn-sm btn-outline-primary mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Study Materials</a>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm rounded-3 p-3 bg-light">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="stat-icon bg-danger-subtle text-danger rounded-3 p-2 fs-5"><i class="fa-solid fa-headset"></i></div>
                                            <h6 class="fw-bold mb-0">Grievances & Notice Board</h6>
                                        </div>
                                        <p class="fs-7 text-muted mb-2">Read official campus notices, exam announcements, and submit support tickets or complaints directly to college administration.</p>
                                        <a href="{{ route('complaints.index') }}" class="btn btn-sm btn-outline-danger mt-auto"><i class="fa-solid fa-arrow-right me-1"></i> Campus Support</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-primary px-4 fw-bold rounded-pill" data-bs-dismiss="modal">
                        <i class="fa-solid fa-check me-1"></i> Got It, Close Guide
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reusable styled confirmation popup — replaces the browser's native confirm() -->
    <div class="modal fade" id="cmsConfirmModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-warning-subtle text-warning" style="width:64px;height:64px;">
                        <i class="fa-solid fa-triangle-exclamation fs-3"></i>
                    </div>
                    <p class="mb-0 fw-semibold text-dark" id="cmsConfirmMessage"></p>
                </div>
                <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal" id="cmsConfirmCancelBtn">Cancel</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold" id="cmsConfirmOkBtn">Yes, Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Choices.js (searchable select / multi-select) -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>
    <script>
        function cmsPreviewPhoto(input, previewId) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = (e) => document.getElementById(previewId).src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }

        function cmsToggleTheme() {
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            const next = isDark ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('cms-theme', next);
            document.getElementById('themeToggleIcon').className = next === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }

        // Icon reflects whatever theme the inline <head> script already applied on load
        document.getElementById('themeToggleIcon').className =
            document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';

        // Turns any <select class="cms-searchable-select"> inside a modal into a searchable
        // (multi-)select once the modal is shown, so Choices.js doesn't mis-measure a hidden element.
        document.addEventListener('show.bs.modal', function (e) {
            e.target.querySelectorAll('select.cms-searchable-select:not([data-choices-init])').forEach(function (el) {
                new Choices(el, {
                    removeItemButton: el.multiple,
                    searchEnabled: true,
                    shouldSort: false,
                    placeholderValue: 'Search & select...',
                    noResultsText: 'No matches found',
                });
                el.setAttribute('data-choices-init', '1');
            });
        });

        // Styled confirmation popup for any <form data-confirm="message text">.
        // Replaces the browser's native confirm() dialog app-wide.
        (function () {
            const modalEl = document.getElementById('cmsConfirmModal');
            const modal = new bootstrap.Modal(modalEl);
            const messageEl = document.getElementById('cmsConfirmMessage');
            const okBtn = document.getElementById('cmsConfirmOkBtn');
            let pendingForm = null;

            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
                if (form.dataset.confirmed === '1') return; // already approved, let it submit

                e.preventDefault();
                pendingForm = form;
                messageEl.textContent = form.getAttribute('data-confirm');
                modal.show();
            });

            okBtn.addEventListener('click', function () {
                modal.hide();
                if (pendingForm) {
                    pendingForm.dataset.confirmed = '1';
                    pendingForm.requestSubmit();
                    pendingForm = null;
                }
            });
        })();
    </script>

    <!-- Real-Time Announcement Banner Container -->
    <div id="cmsRealtimeBannerContainer" class="sticky-top no-print" style="z-index: 1080;"></div>

    <!-- Real-Time Floating Toast Container -->
    <div id="cmsRealtimeToastContainer" class="no-print" style="position: fixed; bottom: 24px; right: 24px; z-index: 1095; display: flex; flex-direction: column; gap: 12px; max-width: 400px; width: 100%; pointer-events: none;"></div>

    <!-- Real-Time Center Modal Popup -->
    <div class="modal fade no-print" id="cmsRealtimeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div id="cmsRealtimeModalHeader" class="modal-header text-white p-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="cmsRealtimeModalTitle">
                        <i class="fa-solid fa-bell animate-bounce"></i> Real-Time Alert
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2 text-muted fs-7">
                        <i class="fa-solid fa-user-shield text-primary"></i> <span id="cmsRealtimeModalSender">Admin</span> &bull; <span id="cmsRealtimeModalTime">Just now</span>
                    </div>
                    <div id="cmsRealtimeModalBody" class="fs-6 text-dark lh-base"></div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-primary px-4 fw-bold rounded-pill" id="cmsRealtimeModalDismissBtn" data-bs-dismiss="modal">
                        <i class="fa-solid fa-check me-1"></i> Acknowledge & Dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>

    @auth
    @if(Auth::user()->isManagement())
    <!-- Admin Live Broadcast Compose Modal -->
    <div class="modal fade no-print" id="cmsAdminBroadcastModal" tabindex="-1" aria-labelledby="cmsAdminBroadcastModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #6d3fa0 0%, #2e5fa3 100%);">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="cmsAdminBroadcastModalLabel">
                        <i class="fa-solid fa-tower-broadcast text-warning fs-4"></i> Broadcast Live Real-Time Message
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('broadcasts.send') }}" method="POST" id="cmsAdminBroadcastForm">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
                            <i class="fa-solid fa-circle-info me-2"></i> Messages sent here will pop up instantly in <strong>real-time</strong> on the screens of all connected users without them having to reload the page!
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Message Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg rounded-3" placeholder="e.g., Urgent Notice: Class Rescheduled" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Broadcast Message Details <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control rounded-3" rows="4" placeholder="Enter the full message for users..." required></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Target Audience</label>
                                <select name="target_role" class="form-select rounded-3">
                                    <option value="all">Everyone (All Users)</option>
                                    <option value="student">Students Only</option>
                                    <option value="staff">Faculty / Staff Only</option>
                                    <option value="management">Management Only</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Alert Type / Color</label>
                                <select name="type" class="form-select rounded-3">
                                    <option value="info">Info (Blue)</option>
                                    <option value="success">Success (Green)</option>
                                    <option value="warning">Warning (Yellow)</option>
                                    <option value="danger">Urgent Alert (Red)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-dark">Display Style</label>
                                <select name="display_format" class="form-select rounded-3">
                                    <option value="toast">Toast Popup (Bottom Right)</option>
                                    <option value="modal">Center Modal Popup</option>
                                    <option value="banner">Top Announcement Bar</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Auto-expire After</label>
                                <select name="expires_in_hours" class="form-select rounded-3">
                                    <option value="">Never (Until Dismissed)</option>
                                    <option value="1">1 Hour</option>
                                    <option value="6">6 Hours</option>
                                    <option value="24" selected>24 Hours</option>
                                    <option value="72">3 Days</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 p-3">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4 rounded-pill shadow-sm">
                            <i class="fa-solid fa-paper-plane me-1"></i> Send Real-Time Broadcast
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endauth

    <script>
        // Real-Time Message Broadcast Client Engine
        (function () {
            @auth
            const REALTIME_CHECK_URL = "{{ route('broadcasts.check') }}";
            const CSRF_TOKEN = "{{ csrf_token() }}";
            const displayedMessageIds = new Set();

            function playNotificationSound() {
                try {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtx) return;
                    const ctx = new AudioCtx();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.15, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.35);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                } catch (e) {}
            }

            function dismissRealtimeMessage(id, elementToDestroy) {
                if (elementToDestroy) elementToDestroy.remove();
                fetch(`/api/realtime-messages/${id}/dismiss`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                }).catch(console.error);
            }

            function renderRealtimeMessage(msg) {
                if (displayedMessageIds.has(msg.id)) return;
                displayedMessageIds.add(msg.id);

                playNotificationSound();

                const badgeBgMap = {
                    info: 'bg-primary',
                    success: 'bg-success',
                    warning: 'bg-warning text-dark',
                    danger: 'bg-danger'
                };
                const borderMap = {
                    info: 'border-primary',
                    success: 'border-success',
                    warning: 'border-warning',
                    danger: 'border-danger'
                };

                if (msg.display_format === 'modal') {
                    const modalEl = document.getElementById('cmsRealtimeModal');
                    if (modalEl) {
                        document.getElementById('cmsRealtimeModalTitle').innerText = msg.title;
                        document.getElementById('cmsRealtimeModalBody').innerText = msg.message;
                        document.getElementById('cmsRealtimeModalSender').innerText = msg.sender;
                        document.getElementById('cmsRealtimeModalTime').innerText = msg.created_at;

                        const headerEl = document.getElementById('cmsRealtimeModalHeader');
                        headerEl.className = `modal-header p-3 text-white ${badgeBgMap[msg.type] || 'bg-primary'}`;

                        const dismissBtn = document.getElementById('cmsRealtimeModalDismissBtn');
                        dismissBtn.onclick = function() {
                            dismissRealtimeMessage(msg.id, null);
                        };

                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bsModal.show();
                    }
                } else if (msg.display_format === 'banner') {
                    const bannerContainer = document.getElementById('cmsRealtimeBannerContainer');
                    if (bannerContainer) {
                        const banner = document.createElement('div');
                        banner.className = `alert alert-${msg.type === 'danger' ? 'danger' : (msg.type === 'warning' ? 'warning' : 'info')} alert-dismissible fade show rounded-0 border-0 shadow-sm m-0 p-3 text-center fw-semibold`;
                        banner.style.zIndex = '1080';
                        banner.innerHTML = `
                            <div class="container d-flex align-items-center justify-content-center gap-2">
                                <i class="fa-solid fa-tower-broadcast animate-pulse fs-5"></i>
                                <span><strong>${msg.title}:</strong> ${msg.message}</span>
                                <button type="button" class="btn-close position-relative p-0 ms-3" aria-label="Close"></button>
                            </div>
                        `;
                        banner.querySelector('.btn-close').onclick = function() {
                            dismissRealtimeMessage(msg.id, banner);
                        };
                        bannerContainer.appendChild(banner);
                    }
                } else {
                    // Default: Toast Popup
                    const toastContainer = document.getElementById('cmsRealtimeToastContainer');
                    if (toastContainer) {
                        const actionBtn = msg.action_url ? `<div class="mt-2 mb-1"><a href="${msg.action_url}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fs-7 fw-bold shadow-sm d-inline-flex align-items-center gap-1"><i class="fa-solid fa-arrow-up-right-from-square fs-8"></i> Open / View</a></div>` : '';
                        const toast = document.createElement('div');
                        toast.style.pointerEvents = 'auto';
                        toast.className = `card border-start border-4 ${borderMap[msg.type] || 'border-primary'} shadow-lg rounded-4 overflow-hidden animate-slide-up bg-white`;
                        toast.innerHTML = `
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <span class="badge ${badgeBgMap[msg.type] || 'bg-primary'} rounded-pill"><i class="fa-solid fa-bell"></i></span>
                                        <span>${msg.title}</span>
                                    </div>
                                    <button type="button" class="btn-close fs-7" aria-label="Close"></button>
                                </div>
                                <div class="text-secondary small mb-1 lh-sm">${msg.message}</div>
                                ${actionBtn}
                                <div class="d-flex align-items-center justify-content-between text-muted fs-7 border-top pt-2 mt-2">
                                    <span><i class="fa-solid fa-user-shield text-primary me-1"></i>${msg.sender} &bull; ${msg.created_at}</span>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none fs-7 fw-bold text-primary dismiss-btn">Dismiss</button>
                                </div>
                            </div>
                        `;

                        const handleDismiss = function() {
                            dismissRealtimeMessage(msg.id, toast);
                        };
                        toast.querySelector('.btn-close').onclick = handleDismiss;
                        toast.querySelector('.dismiss-btn').onclick = handleDismiss;

                        toastContainer.appendChild(toast);
                    }
                }
            }

            function checkRealtimeMessages() {
                fetch(REALTIME_CHECK_URL, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.ok ? res.json() : null)
                .then(data => {
                    if (data && data.messages && data.messages.length > 0) {
                        data.messages.forEach(renderRealtimeMessage);
                    }
                })
                .catch(console.error);
            }

            // Start checking every 3 seconds
            setInterval(checkRealtimeMessages, 3000);
            // Initial check 500ms after page load
            setTimeout(checkRealtimeMessages, 500);
            @endauth
        })();
    </script>
    @yield('scripts')
</body>
</html>
