<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Account - {{ $siteSettings->site_name ?? 'College Management System' }}</title>
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
            position: relative;
        }
        .login-card {
            background: #ffffff;
            border-radius: var(--cms-radius-2xl);
            box-shadow: var(--cms-shadow-2xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 520px;
            position: relative;
            z-index: 10;
        }
        .login-header {
            background: var(--cms-gradient-brand);
            padding: 2rem;
            color: #ffffff;
            text-align: center;
        }
        .account-btn {
            border: 1px solid var(--cms-border);
            border-radius: var(--cms-radius-lg);
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            width: 100%;
            background: #f8fafc;
            text-align: left;
            transition: all var(--cms-transition-fast);
        }
        .account-btn:hover {
            border-color: var(--cms-primary);
            background: var(--cms-primary-light);
            transform: translateY(-2px);
            box-shadow: var(--cms-shadow-md);
        }
        .badge-role-management { background: #f43f5e; color: #fff; }
        .badge-role-staff { background: var(--cms-secondary); color: #fff; }
        .badge-role-student { background: var(--cms-accent-teal); color: #fff; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/campus.css') }}?v={{ filemtime(public_path('css/campus.css')) }}">
</head>
<body class="campus-auth">
    <div class="brand-strip fixed-top"></div>

    <div class="login-card animate-fade-up">
        <div class="login-header">
            <i class="fa-solid fa-users text-warning fs-1 mb-2" aria-hidden="true"></i>
            <h4 class="fw-bold mb-1">Multiple Accounts Found</h4>
            <p class="text-white-50 small mb-0">This phone number is linked to more than one account. Choose which one to sign in as.</p>
        </div>

        <div class="p-4 p-sm-5">
            <div class="d-flex flex-column gap-3">
                @foreach($accounts as $account)
                <form action="{{ route('login.choose.select', $account->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="account-btn">
                        <img src="{{ $account->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($account->name) }}" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">{{ $account->name }}</div>
                            <div class="text-muted fs-7">
                                @if($account->studentProfile)
                                    {{ $account->studentProfile->enrollment_no }} &middot; {{ optional($account->studentProfile->course)->name }}
                                @else
                                    {{ $account->email }}
                                @endif
                            </div>
                        </div>
                        <span class="badge rounded-pill badge-role-{{ $account->role }} text-uppercase fs-7">{{ $account->role }}</span>
                    </button>
                </form>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-muted fs-7 text-decoration-none"><i class="fa-solid fa-arrow-left me-1"></i> Back to login</a>
            </div>
        </div>
    </div>

</body>
</html>
