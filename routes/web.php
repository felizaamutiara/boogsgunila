<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GedungController;
use App\Http\Controllers\FasilitasController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GoogleAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// Public routes (accessible without authentication)
Route::get('/home', [PublicController::class, 'home'])->name('home');
Route::get('/availability', [PublicController::class, 'availability'])->name('public.availability');
Route::get('/quote', [PublicController::class, 'quote'])->name('public.quote');
Route::view('/tentang', 'tentang')->name('tentang');
Route::get('/jadwal', [PublicController::class, 'jadwal'])->name('public.jadwal');

// Public sewa pages (use controller to provide data)
Route::get('/sewa/gedung', [PublicController::class, 'gedung'])->name('public.sewa.gedung');
Route::get('/sewa/fasilitas', [PublicController::class, 'fasilitas'])->name('public.sewa.fasilitas');

// Guest only routes (only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/', [PublicController::class, 'home']);

    // Authentication routes
    Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

    // OTP verification
    Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('auth.otp.form');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');
    Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('auth.otp.resend');

    // DEBUG: Show OTP for testing (remove in production)
    Route::get('/debug-otp/{email}', function ($email) {
        if (app()->environment('production')) {
            abort(403);
        }
        $otp = \App\Models\MahasiswaOtp::where('email', $email)
            ->latest()
            ->first();

        if (!$otp) {
            return 'OTP tidak ditemukan untuk email: ' . $email;
        }

        return "Email: {$otp->email}<br>OTP: <strong>{$otp->otp}</strong><br>Expired: {$otp->expired_at}";
    })->name('debug.otp');

    // DEBUG: Manually verify user by email
    Route::post('/debug-verify/{email}', function ($email) {
        if (app()->environment('production')) {
            abort(403);
        }
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return "User not found: $email";
        }
        $user->update(['email_verified_at' => now()]);
        return "User $email verified! Now you can login.";
    })->name('debug.verify');
});

// Authenticated routes (require login)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Booking management
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::put('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/invoice', [BookingController::class, 'invoice'])->name('invoice');
    });

    // Payment management
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::get('/{id}/upload', [PaymentController::class, 'showUploadForm'])->name('upload.form');
        Route::post('/{id}/upload', [PaymentController::class, 'uploadProof'])->name('upload');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Admin routes (admin only - require role 'A')
Route::middleware(['auth', 'role:A'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Admin Users CRUD
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'usersIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'usersCreate'])->name('create');
        Route::post('/', [AdminController::class, 'usersStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'usersEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'usersUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'usersDestroy'])->name('destroy');
    });

    // Admin Schedules & Rentals
    Route::get('/schedules', [AdminController::class, 'schedulesIndex'])->name('schedules.index');
    Route::get('/rentals', [AdminController::class, 'rentalsIndex'])->name('rentals.index');

    // Admin Booking Actions
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::put('/{id}/approve', [AdminController::class, 'approveBooking'])->name('approve');
        Route::put('/{id}/reject', [AdminController::class, 'rejectBooking'])->name('reject');
    });

    // Admin create booking (uses admin layout)
    Route::get('/booking/create', [AdminController::class, 'bookingCreate'])->name('booking.create');
    Route::post('/booking', [AdminController::class, 'bookingStore'])->name('booking.store');
    Route::get('/booking/{id}/edit', [AdminController::class, 'bookingEdit'])->name('booking.edit');
    Route::put('/booking/{id}', [AdminController::class, 'bookingUpdate'])->name('booking.update');
    Route::delete('/booking/{id}', [AdminController::class, 'bookingDestroy'])->name('booking.destroy');
    Route::get('/booking/{id}/invoice', [AdminController::class, 'bookingInvoice'])->name('booking.invoice');

    // Payment Account Management (for admin to add/edit bank & e-wallet accounts)
    Route::prefix('payment-accounts')->name('payment_accounts.')->group(function () {
        Route::get('/', [AdminController::class, 'paymentAccountsIndex'])->name('index');
        Route::get('/create', [AdminController::class, 'paymentAccountsCreate'])->name('create');
        Route::post('/', [AdminController::class, 'paymentAccountsStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'paymentAccountsEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'paymentAccountsUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'paymentAccountsDestroy'])->name('destroy');
    });

    // Payment Verification
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', [PaymentController::class, 'adminIndex'])->name('index');
        Route::put('/{id}/status', [PaymentController::class, 'adminMark'])->name('status');
    });
});

// Admin-only resource routes (Gedung & Fasilitas management)
Route::middleware(['auth', 'role:A'])->group(function () {
    // DEBUG: Check users email_verified_at status (remove in production)
    Route::get('/debug-users', function () {
        if (app()->environment('production')) {
            abort(403);
        }
        $users = \App\Models\User::select('id', 'name', 'email', 'role', 'email_verified_at')->get();
        return view('debug.users', ['users' => $users]);
    })->name('debug.users');

    // DEBUG: Fix all users email_verified_at (set all to now)
    Route::post('/debug-fix-users', function () {
        if (app()->environment('production')) {
            abort(403);
        }
        $count = \App\Models\User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);
        return "Fixed $count users. All users now have email_verified_at set.";
    })->name('debug.fix.users');

    // Gedung management
    Route::prefix('gedung')->name('gedung.')->group(function () {
        Route::get('/', [GedungController::class, 'index'])->name('index');
        Route::get('/create', [GedungController::class, 'create'])->name('create');
        Route::post('/', [GedungController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [GedungController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GedungController::class, 'update'])->name('update');
        Route::delete('/{id}', [GedungController::class, 'destroy'])->name('destroy');
    });

    // Fasilitas management
    Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
        Route::get('/', [FasilitasController::class, 'index'])->name('index');
        Route::get('/create', [FasilitasController::class, 'create'])->name('create');
        Route::post('/', [FasilitasController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [FasilitasController::class, 'edit'])->name('edit');
        Route::put('/{id}', [FasilitasController::class, 'update'])->name('update');
        Route::delete('/{id}', [FasilitasController::class, 'destroy'])->name('destroy');
    });
});
