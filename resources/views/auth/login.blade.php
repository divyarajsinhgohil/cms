<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ $siteSettings->site_name ?? 'College Management System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #6d3fa0;
            --bs-primary-rgb: 109, 63, 160;
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
            --bs-btn-focus-shadow-rgb: 138, 106, 179;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #2a1240 0%, #2e5fa3 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-strip {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: linear-gradient(90deg, #f5841f 0%, #e6007e 25%, #6d3fa0 50%, #2e5fa3 75%, #2ba84a 100%);
        }
        .login-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            width: 100%;
            max-width: 460px;
        }
        .login-header {
            background: linear-gradient(135deg, #4a1f6e 0%, #2e5fa3 100%);
            padding: 2.5rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
        }
        .quick-btn {
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .quick-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="brand-strip"></div>

    <div class="login-card">
        <div class="login-header">
            @if(!empty($siteSettings?->logo_url))
                <img src="{{ $siteSettings->logo_url }}" alt="Logo" height="56" class="rounded mb-2">
            @else
                <i class="fa-solid fa-graduation-cap text-warning display-4 mb-2"></i>
            @endif
            <h3 class="fw-bold mb-1">{{ $siteSettings->site_name ?? 'College Management System' }}</h3>
            <p class="text-white-50 small mb-0">{{ $siteSettings->affiliation ?? 'Role-Based Centralized Academic Portal' }}</p>
        </div>

        <div class="p-4 p-sm-5">
            @if(session('error'))
                <div class="alert alert-danger mb-4 fs-7 py-2" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info mb-4 fs-7 py-2" role="alert">
                    <i class="fa-solid fa-circle-info me-1"></i> {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="mb-4">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Email, Mobile Number, or Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-user me-1"></i></span>
                        <input type="text" name="login" class="form-control bg-light border-start-0" placeholder="e.g. student@cms.edu, +91 98765 43212, or your full name" required value="{{ old('login') }}">
                    </div>
                    @error('login') <span class="text-danger fs-7 mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small text-muted">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In to Portal
                </button>
            </form>

            <div class="text-center border-top pt-3">
                <span class="text-muted fs-7">New student?</span>
                <a href="{{ route('admission-applications.create') }}" class="fw-semibold fs-7 text-decoration-none">Apply for Admission</a>
            </div>
        </div>
    </div>

</body>
</html>
