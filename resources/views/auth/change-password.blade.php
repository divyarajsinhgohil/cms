<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - College Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #1e1130 0%, #0f172a 50%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            background: #ffffff;
            border-radius: var(--cms-radius-2xl);
            box-shadow: var(--cms-shadow-2xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
        }
        .card-header-custom {
            background: var(--cms-gradient-brand);
            padding: 2.5rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/campus.css') }}?v={{ filemtime(public_path('css/campus.css')) }}">
</head>
<body class="campus-auth">
    <div class="brand-strip fixed-top"></div>

    <div class="card-custom animate-fade-up">
        <div class="card-header-custom">
            <i class="fa-solid fa-shield-halved text-warning fs-1 mb-2" aria-hidden="true"></i>
            <h3 class="fw-bold mb-1">Set & Confirm New Password</h3>
            <p class="text-white-50 small mb-0">Welcome, {{ Auth::user()->name }}! Please update your default password to secure your account.</p>
        </div>

        <div class="p-4 p-sm-5">
            @if(session('info'))
                <div class="alert alert-warning mb-4 fs-7 py-2" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('password.change.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">Enter New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-key"></i></span>
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Min 8 characters, letters and numbers" required>
                    </div>
                    @error('password') <span class="text-danger fs-7 mt-1 d-block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small text-muted">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password_confirmation" class="form-control bg-light border-start-0" placeholder="Re-enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gradient-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm">
                    <i class="fa-solid fa-circle-check me-2"></i> Confirm Password & Enter Dashboard
                </button>
            </form>
        </div>
    </div>

</body>
</html>
