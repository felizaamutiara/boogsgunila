@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center"
    style="background-image: url('{{ asset('img/GSGunila.jpg') }}')">

    <!-- CARD LOGIN -->
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">

        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

        <form method="POST" action="{{ route('auth.login') }}" class="space-y-4">
            @csrf

            @if($errors->any())
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="mt-1 w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror"
                    required>
            </div>

            <div>
                <label class="block text-sm font-medium">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="login_password"
                        class="mt-1 w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror"
                        required>
                    <button type="button" class="absolute inset-y-0 right-2 px-2 text-gray-600" onclick="togglePassword('login_password', this)" aria-label="Toggle password visibility">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">
    Masuk
</button>

<a href="{{ route('google.redirect') }}"
   class="w-full flex items-center justify-center gap-2 border border-gray-300 py-2 rounded bg-white hover:bg-gray-100 transition">
    <img src="https://www.svgrepo.com/show/475656/google-color.svg" width="22">
    <span class="text-gray-700">Login dengan Google</span>
</a>

    </div>

</div>
@endsection

<script>
    function togglePassword(fieldId, btn) {
        var input = document.getElementById(fieldId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
            }
    }
</script>