<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password - College Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            width: 100%;
            max-width: 480px;
        }
        .card-header-custom {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 2.5rem 2rem 2rem;
            color: #ffffff;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fa-solid fa-shield-key text-warning display-4 mb-2"></i>
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
                        <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Minimum 6 characters" required>
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

                <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm">
                    <i class="fa-solid fa-circle-check me-2"></i> Confirm Password & Enter Dashboard
                </button>
            </form>
        </div>
    </div>

</body>
</html>
