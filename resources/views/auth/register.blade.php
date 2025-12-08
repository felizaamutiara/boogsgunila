@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center" 
     style="background-image: url('{{ asset('img/GSGunila.jpg') }}')">

    <!-- CARD REGISTER -->
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md">
        
        <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>

        <form method="POST" action="{{ route('auth.register') }}" class="space-y-4">
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

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium">Nama</label>
                <input type="text" name="name"
                       class="mt-1 w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror"
                       value="{{ old('name') }}" required>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email"
                       class="mt-1 w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror"
                       value="{{ old('email') }}" required>
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="register_password"
                           class="mt-1 w-full border rounded px-3 py-2 @error('password') border-red-500 @enderror"
                           required>
                    <button type="button" class="absolute inset-y-0 right-2 px-2 text-gray-600" onclick="togglePassword('register_password', this)" aria-label="Toggle password visibility">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Password Confirmation -->
            <div>
                <label class="block text-sm font-medium">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="register_password_confirmation"
                           class="mt-1 w-full border rounded px-3 py-2"
                           required>
                    <button type="button" class="absolute inset-y-0 right-2 px-2 text-gray-600" onclick="togglePassword('register_password_confirmation', this)" aria-label="Toggle password visibility">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol -->
            <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold">
                Daftar
            </button>

        </form>

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