<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Want It - Setup Two-Factor Authentication</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #111827;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #f9fafb;
        }
        .container {
            width: 100%;
            max-width: 28rem;
            padding: 0 1rem;
        }
        .logo { height: 3rem; margin: 0 auto 1rem; display: block; }
        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.5rem; font-weight: 600; }
        .header p { color: #9ca3af; margin-top: 0.5rem; font-size: 0.95rem; }
        .card {
            background: #1f2937;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.3);
            border: 1px solid #374151;
        }
        .alert {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            background: rgba(22,163,74,.2);
            border: 1px solid rgba(22,163,74,.5);
            border-radius: 0.375rem;
            color: #86efac;
            font-size: 0.875rem;
        }
        .qr-wrap { display: flex; justify-content: center; margin-bottom: 1.5rem; }
        .qr-wrap > div { background: #fff; padding: 0.75rem; border-radius: 0.5rem; }
        .secret-box {
            margin-bottom: 1.5rem;
            padding: 0.75rem 1rem;
            background: rgba(55,65,81,.5);
            border-radius: 0.5rem;
        }
        .secret-box p { font-size: 0.875rem; color: #9ca3af; margin-bottom: 0.25rem; }
        .secret-box code { color: #a5b4fc; font-family: monospace; font-size: 0.875rem; word-break: break-all; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; font-size: 0.875rem; font-weight: 500; color: #d1d5db; margin-bottom: 0.5rem; }
        .field input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 0.5rem;
            color: #fff;
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5em;
            outline: none;
            transition: border-color .15s;
        }
        .field input::placeholder { color: #6b7280; letter-spacing: normal; font-size: 1rem; }
        .field input:focus { border-color: #6366f1; }
        .error { color: #f87171; font-size: 0.875rem; margin-bottom: 1rem; text-align: center; }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
        }
        .btn:hover { background: #4338ca; }
        .footer-link { text-align: center; margin-top: 1.5rem; }
        .footer-link button { background: none; border: none; cursor: pointer; color: #6b7280; font-size: 0.875rem; transition: color .15s; }
        .footer-link button:hover { color: #d1d5db; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="/img/logo_iwantit.png" alt="I Want It" class="logo">
            <h1>Setup Two-Factor Authentication</h1>
            <p>Scan the QR code with your authenticator app</p>
        </div>
        <div class="card">
            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif
            <div class="qr-wrap">
                <div>{!! $qrCode !!}</div>
            </div>
            <div class="secret-box">
                <p>Manual setup key:</p>
                <code>{{ $secret }}</code>
            </div>
            <form method="post" action="{{ route('two-factor.setup') }}">
                @csrf
                <div class="field">
                    <label for="code">Verify Code</label>
                    <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="one-time-code" required placeholder="Enter 6-digit code">
                </div>
                @error('code')
                    <p class="error">{{ $message }}</p>
                @enderror
                <button type="submit" class="btn">Enable Two-Factor Authentication</button>
            </form>
            <div class="footer-link">
                <form method="post" action="{{ route('two-factor.disable') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Cancel setup</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
