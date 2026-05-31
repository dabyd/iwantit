<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Want It - Two-Factor Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4">
        <div class="text-center mb-8">
            <img src="/img/logo_iwantit.png" alt="I Want It" class="h-12 mx-auto mb-4">
            <h1 class="text-2xl font-semibold text-white">Two-Factor Authentication</h1>
            <p class="text-gray-400 mt-2">Enter the code from your authenticator app</p>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 shadow-xl border border-gray-700">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-900/50 border border-green-700 text-green-300 rounded text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="post" action="{{ route('two-factor.verify') }}">
                @csrf

                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-300 mb-2">Authentication Code</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        autocomplete="one-time-code"
                        autofocus
                        required
                        placeholder="000000"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white text-center text-2xl tracking-[0.5em] placeholder-gray-500 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                    >
                </div>

                @error('code')
                    <p class="text-red-400 text-sm mb-4 text-center">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition"
                >
                    Verify
                </button>
            </form>

            <div class="mt-6 text-center">
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-300 transition">
                        Cancel and return to login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
