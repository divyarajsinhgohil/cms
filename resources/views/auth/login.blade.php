<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('public.login_title') }} - {{ $siteSettings->site_name ?? 'College Management System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">

    <link rel="stylesheet" href="{{ asset('css/campus.css') }}?v={{ filemtime(public_path('css/campus.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/harivandana-preview.css') }}?v={{ filemtime(public_path('css/harivandana-preview.css')) }}">
</head>
<body class="campus-login">
    <a href="#login-form" class="cms-skip-link">{{ __('public.skip_to_content') }}</a>
    <header class="campus-login-top">
        <a class="campus-wordmark" href="{{ route('public.home') }}">
            <img class="harivandana-logo" src="{{ asset('images/harivandana-logo.png') }}" alt="Harivandana College — affiliated to Saurashtra University">
        </a>
        @include('components.language-switcher')
    </header>
    <main class="campus-login-layout">
        <section class="campus-story" aria-labelledby="campus-story-title">
            <div class="campus-eyebrow">A place to learn. A space to grow.</div>
            <h1 id="campus-story-title">Your campus.<br>Your journey.<br><em>All connected.</em></h1>
            <p>From your first day to your next milestone. Everything you need for college life, together in one place.</p>
            <img class="campus-art" src="{{ asset('images/campus-architecture.svg') }}" alt="" aria-hidden="true" width="640" height="300">
            <div class="campus-story-caption"><span aria-hidden="true">✦</span><span>Learning, community &amp; possibility.</span></div>
        </section>
        <section class="login-card" aria-labelledby="sign-in-heading">
            <div class="campus-login-intro">
                <div class="campus-eyebrow text-primary">THE CAMPUS PORTAL</div>
                <h2 id="sign-in-heading">Welcome back.</h2>
                <p>{{ __('public.login_subtitle') }}. Sign in to continue.</p>
            </div>
        <div id="login-form">

            <!-- Quick Demo Login Shortcuts (only on a local/dev install, never in production) -->
            @if(app()->environment('local'))
            <div class="mb-4">
                <div class="demo-roles-label"><i class="fa-solid fa-bolt text-warning me-1"></i> Explore a demo workspace</div>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <button type="button" class="demo-pill-btn admin-pill" onclick="quickFill('admin@cms.edu', 'password', this)" aria-pressed="false">
                        <i class="fa-solid fa-user-shield text-danger"></i> Admin
                    </button>
                    <button type="button" class="demo-pill-btn staff-pill" onclick="quickFill('faculty@cms.edu', 'password', this)" aria-pressed="false">
                        <i class="fa-solid fa-chalkboard-user text-warning"></i> Faculty
                    </button>
                    <button type="button" class="demo-pill-btn student-pill" onclick="quickFill('student@cms.edu', 'password', this)" aria-pressed="false">
                        <i class="fa-solid fa-user-graduate text-success"></i> Student
                    </button>
                </div>
            </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4 fs-7 py-2.5 px-3 rounded-3" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1.5"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info border-0 shadow-sm mb-4 fs-7 py-2.5 px-3 rounded-3" role="alert">
                    <i class="fa-solid fa-circle-info me-1.5"></i> {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="mb-3">
                @csrf
                <div class="mb-3">
                    <label for="login_input" class="form-label fw-bold small text-secondary mb-1.5">Email or Mobile Number</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="text" id="login_input" name="login" class="form-control" placeholder="Enter your email or mobile number" autocomplete="username" required value="{{ old('login') }}">
                    </div>
                    @error('login') <span class="text-danger fs-7 mt-1.5 d-block fw-semibold">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary mb-1.5" for="password">{{ __('public.password') }}</label>
                    <div class="input-group input-group-custom">
                        <span class="input-group-text" aria-hidden="true"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="pwd-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Toggle Password Visibility">
                            <i class="fa-solid fa-eye" id="pwd_icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit-login w-100 mb-2">
                    <i class="fa-solid fa-right-to-bracket me-2" aria-hidden="true"></i> {{ __('public.sign_in') }}
                </button>
            </form>

            <div class="login-links text-center border-top pt-3 mt-4">
                <span class="text-muted fs-7">{{ __('public.nav_apply') }}?</span>
                <a href="{{ route('admission-applications.create') }}" class="fw-bold fs-7 text-primary text-decoration-none ms-1">{{ __('public.hero_cta_apply') }}</a>
                <span class="mx-2 text-muted opacity-50">·</span>
                <a href="{{ route('public.home') }}" class="fw-semibold fs-7 text-secondary text-decoration-none">{{ __('public.nav_home') }}</a>
            </div>
        </div>
        </section>
    </main>
    <footer class="campus-login-footer">
        <span>&copy; {{ date('Y') }} {{ $siteSettings->site_name ?? 'College Management System' }}</span>
        <span>One campus. Endless possibilities.</span>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function quickFill(login, password, button) {
            document.querySelectorAll('.demo-pill-btn').forEach(function (item) {
                item.classList.toggle('is-selected', item === button);
                item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
            });
            document.getElementById('login_input').value = login;
            document.getElementById('password').value = password;
        }

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('password');
            const pwdIcon = document.getElementById('pwd_icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                pwdIcon.classList.remove('fa-eye');
                pwdIcon.classList.add('fa-eye-slash');
            } else {
                pwdInput.type = 'password';
                pwdIcon.classList.remove('fa-eye-slash');
                pwdIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
