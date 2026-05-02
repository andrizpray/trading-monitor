<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Trading Monitor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #F8FAFC;
            color: #0F172A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            margin: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        }
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo h1 {
            font-size: 1.5rem;
            color: #2563EB;
        }
        .logo p {
            font-size: 0.875rem;
            color: #94A3B8;
            margin-top: 0.25rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            color: #0F172A;
            font-size: 0.9rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: #3B82F6;
            color: #FFFFFF;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #2563EB;
            box-shadow: 0 1px 2px rgba(37,99,235,0.3);
        }
        .error-msg {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            color: #64748B;
        }
        .remember input[type="checkbox"] {
            accent-color: #3B82F6;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">
            <h1>📊 Trading Monitor</h1>
            <p>Dashboard Admin</p>
        </div>

        @if ($errors->any())
            <div class="error-msg">
                {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="admin@trading-monitor.local">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password">
            </div>
            <div class="remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
        </form>
    </div>
</body>
</html>
