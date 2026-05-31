<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I Want It - Setup Two-Factor Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md mx-auto px-4">
        <div class="text-center mb-8">
            <img src="/img/logo_iwantit.png" alt="I Want It" class="h-12 mx-auto mb-4">
            <h1 class="text-2xl font-semibold text-white">Setup Two-Factor Authentication</h1>
            <p class="text-gray-400 mt-2">Scan the QR code with your authenticator app</p>
        </div>

        <div class="bg-gray-800 rounded-lg p-8 shadow-xl border border-gray-700">
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-900/50 border border-green-700 text-green-300 rounded text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex justify-center mb-6">
                <div class="bg-white p-3 rounded-lg">
                    {!! $qrCode !!}
                </div>
            </div>

            <div class="mb-6 p-3 bg-gray-700/50 rounded-lg">
                <p class="text-sm text-gray-400 mb-1">Manual setup key:</p>
                <code class="text-indigo-300 font-mono text-sm break-all">{{ $secret }}</code>
            </div>

            <form method="post" action="{{ route('two-factor.setup') }}">
                @csrf

                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-300 mb-2">Verify Code</label>
                    <input
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        autocomplete="one-time-code"
                        required
                        placeholder="Enter 6-digit code"
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
                    Enable Two-Factor Authentication
                </button>
            </form>

            <div class="mt-6 text-center">
                <form method="post" action="{{ route('two-factor.disable') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-300 transition">
                        Cancel setup
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
