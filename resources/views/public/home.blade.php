<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('public.nav_home') }} - {{ $siteSettings->site_name ?? 'College Management System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <script>
        (function () {
            var savedTheme = localStorage.getItem('cms-theme') || 'light';
            var savedAccent = localStorage.getItem('cms-accent') || 'purple';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
            document.documentElement.setAttribute('data-cms-accent', savedAccent);
        })();
    </script>
    <style>
        .public-nav { background: var(--cms-gradient-brand); }
        .hero-section {
            background: linear-gradient(135deg, rgba(109,63,160,0.92) 0%, rgba(46,95,163,0.95) 100%);
            color: #fff;
            padding: 4rem 0 5rem;
        }
        .feature-icon {
            width: 56px; height: 56px;
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            background: var(--cms-primary-bg-subtle, #ede4f7);
            color: var(--cms-primary, #6d3fa0);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/campus.css') }}?v={{ filemtime(public_path('css/campus.css')) }}">
</head>
<body class="campus-public">
    <a href="#main-content" class="cms-skip-link">{{ __('public.skip_to_content') }}</a>
    <div class="brand-strip"></div>

    <header class="public-nav sticky-top shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-dark container py-2" aria-label="{{ __('public.nav_home') }}">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('public.home') }}">
                @if(!empty($siteSettings?->logo_url))
                    <img src="{{ $siteSettings->logo_url }}" alt="" height="36" class="rounded" aria-hidden="true">
                @else
                    <i class="fa-solid fa-graduation-cap text-warning fs-4" aria-hidden="true"></i>
                @endif
                <span>{{ $siteSettings->site_name ?? 'College Management System' }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav"
                    aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('public.home') }}">{{ __('public.nav_home') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admission-applications.create') }}">{{ __('public.nav_apply') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">{{ __('public.nav_login') }}</a></li>
                    <li class="nav-item ms-lg-2">@include('components.language-switcher')</li>
                </ul>
            </div>
        </nav>
    </header>

    <main id="main-content">
        <section class="hero-section text-center animate-fade-up">
            <div class="container px-3">
                <p class="text-white-50 text-uppercase fw-bold small mb-2">{{ __('public.site_tagline') }}</p>
                <h1 class="display-5 fw-bold mb-3">{{ __('public.hero_title', ['name' => $siteSettings->site_name ?? 'Our College']) }}</h1>
                <p class="lead mx-auto mb-4" style="max-width: 640px;">{{ __('public.hero_subtitle') }}</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('admission-applications.create') }}" class="btn btn-light btn-lg px-4 fw-bold shadow">
                        <i class="fa-solid fa-file-pen me-2" aria-hidden="true"></i>{{ __('public.hero_cta_apply') }}
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 fw-bold">
                        <i class="fa-solid fa-right-to-bracket me-2" aria-hidden="true"></i>{{ __('public.hero_cta_login') }}
                    </a>
                </div>
            </div>
        </section>

        <section class="py-5 bg-body-secondary">
            <div class="container">
                <h2 class="fw-bold text-center mb-5">{{ __('public.features_title') }}</h2>
                <div class="row g-4">
                    @foreach([
                        ['feature_admissions', 'feature_admissions_desc', 'fa-file-signature'],
                        ['feature_academics', 'feature_academics_desc', 'fa-book-open'],
                        ['feature_support', 'feature_support_desc', 'fa-headset'],
                    ] as [$titleKey, $descKey, $icon])
                    <div class="col-md-4">
                        <article class="card card-hover-lift border-0 shadow-sm h-100 p-4 animate-fade-up">
                            <div class="feature-icon mb-3" aria-hidden="true"><i class="fa-solid {{ $icon }}"></i></div>
                            <h3 class="h5 fw-bold">{{ __('public.'.$titleKey) }}</h3>
                            <p class="text-muted mb-0">{{ __('public.'.$descKey) }}</p>
                        </article>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <h2 class="fw-bold mb-4"><i class="fa-solid fa-bullhorn text-primary me-2" aria-hidden="true"></i>{{ __('public.notices_title') }}</h2>
                @forelse($notices as $notice)
                    <article class="card border-0 shadow-sm mb-3 card-hover-lift">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <h3 class="h6 fw-bold mb-0">{{ $notice->title }}</h3>
                                @if($notice->priority === 'urgent')
                                    <span class="badge bg-danger">{{ __('public.notice_urgent') }}</span>
                                @endif
                            </div>
                            <p class="text-muted mb-2">{{ Str::limit($notice->content, 200) }}</p>
                            <time class="text-muted small" datetime="{{ $notice->created_at->toIso8601String() }}">
                                {{ $notice->created_at->format('M d, Y') }}
                            </time>
                        </div>
                    </article>
                @empty
                    <p class="text-muted">{{ __('public.notices_empty') }}</p>
                @endforelse
            </div>
        </section>
    </main>

    <footer class="py-4 bg-dark text-white-50 text-center">
        <div class="container">
            <p class="mb-0 small">&copy; {{ date('Y') }} {{ $siteSettings->site_name ?? 'CMS' }}. {{ __('public.footer_rights') }}</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
