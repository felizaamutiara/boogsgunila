<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'profile_photo' => ['nullable','image','mimes:jpg,jpeg,png','max:4096'],
            'current_password' => ['required_with:password', 'nullable', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'password' => ['nullable', 'required_with:current_password', Password::defaults(), 'confirmed'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
    $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('profiles', 'public');

            // delete old file if it was stored under storage
            if ($user->profile_photo_url && str_contains($user->profile_photo_url, '/storage/')) {
                $oldPath = substr($user->profile_photo_url, strpos($user->profile_photo_url, '/storage/') + strlen('/storage/'));
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $user->profile_photo_url = asset('storage/' . $path);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
