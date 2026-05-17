<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Perpustakaan Digital SMKN 40 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f8fafc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { border: none; border-radius: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 420px; padding: 40px; background: white; }
        .btn-login { background: #f59e0b; color: white; border: none; border-radius: 12px; padding: 12px; font-weight: bold; width: 100%; transition: 0.3s; }
        .btn-login:hover { background: #d97706; color: white; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #e2e8f0; }
        .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }
        .logo-placeholder { width: 64px; height: 64px; background: #e2e8f0; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; color: #64748b; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="mb-2">
            <img src="{{ asset('logo-smk.png') }}" alt="Logo" class="img-fluid" style="width: 64px; height: auto;" onerror="this.style.display='none'; document.getElementById('alt-logo').style.display='inline-flex';">
            <div id="alt-logo" class="logo-placeholder" style="display: none;">
                <i class="bi bi-mortarboard-fill" style="font-size: 2rem;"></i>
            </div>
        </div>
        <h3 class="fw-bold m-0">Login</h3>
        <p class="text-muted small">Perpustakaan Digital SMKN 40 Jakarta</p>
    </div>

    @if ($errors->has('login_error'))
        <div class="alert alert-danger p-2 small text-center rounded-3 mb-3">
            {{ $errors->first('login_error') }}
        </div>
    @endif

    <form action="{{ route('login.proses') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Username</label>
            <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required autocomplete="off">
        </div>
        <div class="mb-4">
            <label class="form-label small fw-bold text-muted">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>

        <button type="submit" class="btn-login">LOGIN</button>
    </form>

    <div class="text-center mt-4">
        <p class="text-muted small mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-warning fw-bold text-decoration-none">Register</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>