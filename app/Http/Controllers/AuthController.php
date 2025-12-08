<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MahasiswaOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function __construct()
    {
        // Guest: hanya untuk login + register + OTP
        $this->middleware('guest')->only([
            'showLogin',
            'login',
            'showRegister',
            'register',
            'showOtpForm',
            'verifyOtp'
        ]);

        // Auth: hanya logout
        $this->middleware('auth')->only('logout');
    }

    public function showRegister()
    {
        return view('auth.register', ['title' => 'Register']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required'     => 'Nama harus diisi',
            'email.required'    => 'Email harus diisi',
            'email.email'       => 'Format email tidak valid',
            'email.unique'      => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min'      => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Check if OTP verification is enabled
        $otpEnabled = env('OTP_ENABLED', false);

        if ($otpEnabled) {
            // OTP Enabled - require email verification
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => bcrypt($data['password']),
                'role'     => 'U',
                // Set a consistent default avatar for all new users
                'profile_photo_url' => asset('img/admin-avatar.svg'),
                // email_verified_at is NULL - user must verify via OTP
            ]);

            // Generate OTP
            $otp = rand(100000, 999999);
            MahasiswaOtp::create([
                'email'      => $data['email'],
                'otp'        => $otp,
                'expired_at' => now()->addMinutes(10),
            ]);

            // Kirim email OTP
            Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode OTP Verifikasi Akun - Boo GSG');
            });

            return redirect()->route('auth.otp.form', ['email' => $user->email])
                ->with('success', 'Kode OTP telah dikirim ke email Anda. Silakan check inbox/spam.');
        } else {
            // OTP Disabled - auto-verify user (development mode)
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => bcrypt($data['password']),
                'role'     => 'U',
                'email_verified_at' => now(), // Auto-verify
                // Set a consistent default avatar for all new users
                'profile_photo_url' => asset('img/admin-avatar.svg'),
            ]);

            // Login user langsung setelah register
            Auth::login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Akun Anda berhasil dibuat! Selamat datang di BooGSG.');
        }
    }

    public function showOtpForm(Request $request)
    {
        return view('auth.verify-otp', [
            'title' => 'Verifikasi OTP',
            'email' => $request->email
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        // Cari OTP untuk email
        $otp = MahasiswaOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp' => 'OTP salah']);
        }

        if (now()->greaterThan($otp->expired_at)) {
            return back()->withErrors(['otp' => 'OTP telah kedaluwarsa']);
        }

        // Hapus OTP setelah valid
        $otp->delete();

        // Mark email sebagai verified
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['email_verified_at' => now()]);
        }

        // Login user yang sudah terverifikasi
        Auth::login($user);

        // Redirect based on role
        if ($user->role === 'A') {
            return redirect()->route('admin.dashboard')->with('success', 'Akun Anda berhasil diverifikasi! Selamat datang.');
        }
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi! Silakan mulai booking.');
    }

    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }

        if ($user->email_verified_at) {
            return back()->with('info', 'Email Anda sudah terverifikasi. Silakan login.');
        }

        // Delete old OTPs
        MahasiswaOtp::where('email', $request->email)->delete();

        // Generate new OTP
        $otp = rand(100000, 999999);

        // Save new OTP
        MahasiswaOtp::create([
            'email'      => $request->email,
            'otp'        => $otp,
            'expired_at' => now()->addMinutes(10),
        ]);

        // Send email
        Mail::send('emails.otp', ['otp' => $otp, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email)->subject('Kode OTP Verifikasi Akun - Boo GSG');
        });

        return back()->with('success', 'Kode OTP baru telah dikirim ke email Anda. Silakan check inbox/spam.');
    }

    public function showLogin()
    {
        return view('auth.login', ['title' => 'Login']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if email is verified
            if (!$user->email_verified_at) {
                Auth::logout();
                return redirect()->route('auth.otp.form', ['email' => $user->email])
                    ->with('info', 'Akun Anda belum diverifikasi. Silakan masukkan kode OTP yang telah dikirim ke email Anda.');
            }

            if ($user->role === 'A') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Kredensial tidak valid']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}
