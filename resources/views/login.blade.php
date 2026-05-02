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
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            margin: 1rem;
        }
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo h1 {
            font-size: 1.5rem;
            color: #22d3ee;
        }
        .logo p {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            font-size: 0.875rem;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #22d3ee;
        }
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: #22d3ee;
            color: #0f172a;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-login:hover {
            background: #06b6d4;
        }
        .error-msg {
            background: #7f1d1d;
            border: 1px solid #dc2626;
            color: #fca5a5;
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
            color: #94a3b8;
        }
        .remember input {
            accent-color: #22d3ee;
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
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password">
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
