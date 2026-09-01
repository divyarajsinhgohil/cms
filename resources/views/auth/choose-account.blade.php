<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Account - {{ $siteSettings->site_name ?? 'College Management System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
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
            max-width: 520px;
        }
        .login-header {
            background: linear-gradient(135deg, #4a1f6e 0%, #2e5fa3 100%);
            padding: 2rem;
            color: #ffffff;
            text-align: center;
        }
        .account-btn {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            background: #f8fafc;
            text-align: left;
            transition: all 0.15s ease;
        }
        .account-btn:hover {
            border-color: #6d3fa0;
            background: #ede4f7;
            transform: translateY(-1px);
        }
        .badge-role-management { background: #f43f5e; color: #fff; }
        .badge-role-staff { background: #2e5fa3; color: #fff; }
        .badge-role-student { background: #2ba84a; color: #fff; }
    </style>
</head>
<body>
    <div class="brand-strip"></div>

    <div class="login-card">
        <div class="login-header">
            <i class="fa-solid fa-users text-warning display-5 mb-2"></i>
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
