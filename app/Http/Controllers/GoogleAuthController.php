<?php

namespace App\Http\Controllers;

use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    // Redirect ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Callback dari Google
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        // Cari user berdasarkan email
        $user = User::where('email', $googleUser->getEmail())->first();

        // Jika user belum ada → buat baru
        if (!$user) {
            $user = User::create([
                'name'     => $googleUser->getName(),
                'email'    => $googleUser->getEmail(),
                'password' => bcrypt('google_login_' . now()), // Random password
                'role'     => 'U',
            ]);
        }

        // Login user
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Login Google berhasil!');
    }
}
