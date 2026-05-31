<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    public function __construct(
        protected Google2FA $google2fa = new Google2FA,
    ) {}

    public function showChallenge()
    {
        if (! session()->has('two_factor.login_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $userId = session()->get('two_factor.login_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        if ($this->google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            session()->forget('two_factor.login_id');
            session()->put('two_factor.confirmed', true);

            Auth::login($user, session()->pull('two_factor.remember', false));

            return redirect()->intended('/projects')->with('status', 'You are logged in');
        }

        throw ValidationException::withMessages([
            'code' => 'The verification code is invalid.',
        ]);
    }

    public function showSetup(Request $request)
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('status', '2FA is already enabled.');
        }

        $secret = session()->get('two_factor.setup_secret') ?: $this->google2fa->generateSecretKey();
        session()->put('two_factor.setup_secret', $secret);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $qrCode = $this->generateQrCodeSvg($qrCodeUrl);

        return view('auth.two-factor-setup', [
            'secret' => $secret,
            'qrCode' => $qrCode,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $user = $request->user();
        $secret = session()->get('two_factor.setup_secret');

        if (! $secret || ! $this->google2fa->verifyKey($secret, $request->code)) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is invalid. Scan the QR code again.',
            ]);
        }

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($this->generateRecoveryCodes()),
            'two_factor_confirmed_at' => now(),
        ])->save();

        session()->forget('two_factor.setup_secret');
        session()->put('two_factor.confirmed', true);

        return redirect()->intended('/projects')->with('status', 'Two-factor authentication enabled.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        session()->forget('two_factor.confirmed');

        return back()->with('status', 'Two-factor authentication disabled.');
    }

    protected function generateRecoveryCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < 8; $i++) {
            $codes[] = bin2hex(random_bytes(5)).'-'.bin2hex(random_bytes(5));
        }

        return $codes;
    }

    protected function generateQrCodeSvg(string $url): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200, 0),
            new SvgImageBackEnd
        );

        return (new Writer($renderer))->writeString($url);
    }
}
