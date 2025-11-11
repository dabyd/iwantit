<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {

    public function login( Request $request ) {
        $credentials = $request->validate([
            'email' => [ 'required', 'email', 'string' ],
            'password' => [ 'required', 'string' ]
        ]);

        $remember = $request->filled( 'remember' );

        if ( Auth::attempt( $credentials, $remember ) ) {
            $request->session()->regenerate();

//            return redirect()->intended('/projects')->with('status', 'You are logged in');
            return redirect()->to('/projects')->with('status', 'You are logged in');
        }


        throw ValidationException::withMessages([
            'email' => __( 'auth.failed' )
        ]);

    }

    public function logout( Request $request ) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to('/')->with('status', 'Has cerrado la sesión');
    }
}
