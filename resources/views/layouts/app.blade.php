<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        // Applied before first paint so there is no flash of the wrong theme.
        (function () {
            var saved = localStorage.getItem('cms-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', saved);
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

        /* ---------------- Dark theme ---------------- */
        [data-bs-theme="dark"] {
            --bs-primary-bg-subtle: #3a2a52;
            --bs-primary-border-subtle: #5c3f82;
            --bs-primary-text-emphasis: #d8c2ef;
        }
        [data-bs-theme="dark"] body {
            background-color: #0f0f1a;
            color: #e2e8f0;
        }
        [data-bs-theme="dark"] .sidebar {
            background: #16161f;
            border-right-color: #2a2a3a;
        }
        [data-bs-theme="dark"] .sidebar .nav-link {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .sidebar .nav-link:hover,
        [data-bs-theme="dark"] .sidebar .nav-link.active {
            background: #2a2043;
            color: #c9a8e8;
        }
        [data-bs-theme="dark"] .nav-section-label {
            color: #64748b !important;
        }
        [data-bs-theme="dark"] .nav-group-header:hover {
            color: #c9a8e8 !important;
        }
        [data-bs-theme="dark"] .stat-card {
            background: #1a1a26;
            border-color: #2a2a3a;
        }
        /* .bg-white is used throughout the app as a card-header background and is
           not theme-aware in Bootstrap by design (it is a literal color, not a
           token) — overridden here so every card header follows dark mode too. */
        [data-bs-theme="dark"] .bg-white {
            background-color: #1a1a26 !important;
        }
        [data-bs-theme="dark"] .table {
            --bs-table-color: #e2e8f0;
        }
        [data-bs-theme="dark"] .text-dark {
            color: #e2e8f0 !important;
        }
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #14141f;
            border-color: #2a2a3a;
            color: #e2e8f0;
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
        }
        [data-bs-theme="dark"] .card-header {
            border-color: #2a2a3a !important;
        }
        [data-bs-theme="dark"] .table thead th {
            color: #94a3b8;
        }
        [data-bs-theme="dark"] .table-hover > tbody > tr:hover > * {
            background-color: rgba(201, 168, 232, 0.06);
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
</head>
<body>
    <div class="brand-strip no-print"></div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top no-print">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white fs-4" href="{{ route('dashboard') }}">
                @if(!empty($siteSettings?->logo_url))
                    <img src="{{ $siteSettings->logo_url }}" alt="Logo" height="32" class="rounded">
                @else
                    <i class="fa-solid fa-graduation-cap text-warning fs-3"></i>
                @endif
                <span>{{ $siteSettings->site_name ?? 'CMS College of Technology' }}</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="ms-auto d-flex align-items-center gap-3">
                    <button type="button" class="theme-toggle-btn" onclick="cmsToggleTheme()" title="Toggle dark mode">
                        <i id="themeToggleIcon" class="fa-solid fa-moon"></i>
                    </button>
                    @auth
                    <div class="d-flex align-items-center gap-2 text-white">
                        <span class="badge rounded-pill badge-role-{{ Auth::user()->role }} text-uppercase px-2 py-1 fs-7">
                            {{ Auth::user()->role }}
                        </span>
                        <div class="text-end leading-tight">
                            <div class="fw-semibold small">{{ Auth::user()->name }}</div>
                            <div class="text-white-50 fs-7">{{ Auth::user()->email }}</div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="ms-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger text-white rounded-pill px-3">
                                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0 no-print">
                <div class="py-3">
                    <div class="nav-section-label px-3 mb-3 text-uppercase">Navigation Menu</div>
                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fa-solid fa-chart-pie me-2"></i> Dashboard
                        </a>

                        @if(Auth::user()->isManagement())
                        @php $academicSetupOpen = request()->routeIs(['academics.*', 'sections.*']); @endphp
                        <div class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupAcademicSetup" role="button" aria-expanded="{{ $academicSetupOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-layer-group me-1"></i> Academic Setup</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </div>
                        <div class="collapse {{ $academicSetupOpen ? 'show' : '' }}" id="groupAcademicSetup">
                            <a class="nav-link {{ request()->routeIs('academics.departments') ? 'active' : '' }}" href="{{ route('academics.departments') }}">
                                <i class="fa-solid fa-building-columns me-2"></i> Departments
                            </a>
                            <a class="nav-link {{ request()->routeIs('academics.courses') ? 'active' : '' }}" href="{{ route('academics.courses') }}">
                                <i class="fa-solid fa-graduation-cap me-2"></i> Degree Courses
                            </a>
                            <a class="nav-link {{ request()->routeIs('academics.subjects') ? 'active' : '' }}" href="{{ route('academics.subjects') }}">
                                <i class="fa-solid fa-book-open me-2"></i> Subjects & Curriculum
                            </a>
                            <a class="nav-link {{ request()->routeIs('academics.staff') ? 'active' : '' }}" href="{{ route('academics.staff') }}">
                                <i class="fa-solid fa-user-tie me-2"></i> Faculty & Teachers
                            </a>
                            <a class="nav-link {{ request()->routeIs('sections.*') ? 'active' : '' }}" href="{{ route('sections.index') }}">
                                <i class="fa-solid fa-chalkboard me-2"></i> Class Sections
                            </a>
                        </div>
                        @endif

                        @php
                            $studentGroupRoutes = ['admissions.*', 'admission-applications.*', 'certificates.*', 'fees.*', 'attendance.*'];
                            $studentGroupOpen = request()->routeIs($studentGroupRoutes);
                        @endphp
                        <div class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupStudent" role="button" aria-expanded="{{ $studentGroupOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-user-graduate me-1"></i> Student Management</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </div>
                        <div class="collapse {{ $studentGroupOpen ? 'show' : '' }}" id="groupStudent">
                            @if(!Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('admissions.*') ? 'active' : '' }}" href="{{ route('admissions.index') }}">
                                <i class="fa-solid fa-id-card me-2"></i> Student Directory
                            </a>
                            @endif

                            @if(Auth::user()->isManagement())
                            <a class="nav-link {{ request()->routeIs('admission-applications.*') ? 'active' : '' }}" href="{{ route('admission-applications.index') }}">
                                <i class="fa-solid fa-file-signature me-2"></i> Admission Applications
                                @php $pendingAppsCount = \App\Models\AdmissionApplication::where('status', 'Pending')->count(); @endphp
                                @if($pendingAppsCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-1">{{ $pendingAppsCount }}</span>
                                @endif
                            </a>
                            @endif

                            <a class="nav-link {{ request()->routeIs('certificates.*') ? 'active' : '' }}" href="{{ route('certificates.index') }}">
                                <i class="fa-solid fa-certificate me-2"></i> {{ Auth::user()->isStudent() ? 'My Certificates' : 'Certificates' }}
                            </a>

                            @if(Auth::user()->isManagement())
                            <a class="nav-link {{ request()->routeIs('fees.*') ? 'active' : '' }}" href="{{ route('fees.index') }}">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i> Fees & Dues
                            </a>
                            @endif

                            @if(!Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('attendance.*') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                                <i class="fa-solid fa-clipboard-user me-2"></i> Attendance Log
                            </a>
                            @endif
                        </div>

                        @php $academicsOpen = request()->routeIs(['exams.*', 'timetables.*', 'materials.*']); @endphp
                        <div class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupAcademics" role="button" aria-expanded="{{ $academicsOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-book me-1"></i> Academics & Learning</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </div>
                        <div class="collapse {{ $academicsOpen ? 'show' : '' }}" id="groupAcademics">
                            <a class="nav-link {{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">
                                <i class="fa-solid fa-award me-2"></i> Exam Marks & CGPA
                            </a>
                            <a class="nav-link {{ request()->routeIs('timetables.*') ? 'active' : '' }}" href="{{ route('timetables.index') }}">
                                <i class="fa-solid fa-calendar-days me-2"></i> Timetable Grid
                            </a>
                            <a class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}" href="{{ route('materials.index') }}">
                                <i class="fa-solid fa-book-bookmark me-2"></i> Materials & Tasks
                            </a>
                        </div>

                        @php $campusOpen = request()->routeIs(['leaves.*', 'complaints.*', 'notices.*']); @endphp
                        <div class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupCampus" role="button" aria-expanded="{{ $campusOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-building me-1"></i> Campus Support</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </div>
                        <div class="collapse {{ $campusOpen ? 'show' : '' }}" id="groupCampus">
                            @unless(Auth::user()->isStudent())
                            <a class="nav-link {{ request()->routeIs('leaves.*') ? 'active' : '' }}" href="{{ route('leaves.index') }}">
                                <i class="fa-solid fa-calendar-check me-2"></i> Leave Management
                            </a>
                            @endunless
                            <a class="nav-link {{ request()->routeIs('complaints.*') ? 'active' : '' }}" href="{{ route('complaints.index') }}">
                                <i class="fa-solid fa-headset me-2"></i> Grievances & Support
                            </a>
                            <a class="nav-link {{ request()->routeIs('notices.*') ? 'active' : '' }}" href="{{ route('notices.index') }}">
                                <i class="fa-solid fa-bullhorn me-2"></i> Notice Board
                            </a>
                        </div>

                        @if(Auth::user()->isManagement())
                        @php $outreachOpen = request()->routeIs(['marketing.*', 'settings.*']); @endphp
                        <div class="nav-group-header nav-section-label px-3 py-2 text-uppercase d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#groupOutreach" role="button" aria-expanded="{{ $outreachOpen ? 'true' : 'false' }}">
                            <span><i class="fa-solid fa-tower-broadcast me-1"></i> Outreach & Settings</span>
                            <i class="fa-solid fa-chevron-down fs-8"></i>
                        </div>
                        <div class="collapse {{ $outreachOpen ? 'show' : '' }}" id="groupOutreach">
                            <a class="nav-link {{ request()->routeIs('marketing.*') ? 'active' : '' }}" href="{{ route('marketing.index') }}">
                                <i class="fa-brands fa-whatsapp me-2 text-success"></i> SMS & WhatsApp
                            </a>
                            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">
                                <i class="fa-solid fa-gear me-2"></i> Website Settings
                            </a>
                        </div>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9 col-lg-10 p-4 main-content">
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
    @yield('scripts')
</body>
</html>
