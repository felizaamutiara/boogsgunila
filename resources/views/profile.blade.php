@php $layout = (auth()->check() && auth()->user()->role === 'A') ? 'admin.layout' : 'layouts.app'; @endphp
@extends($layout)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Profil Saya</h1>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="p-8">
                <div class="flex items-center space-x-8 mb-8">
                    <div class="flex-shrink-0">
                            <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <img src="{{ auth()->user()->profile_photo_url ?? asset('img/admin-avatar.svg') }}" alt="Profile Photo" class="w-full h-full object-cover" data-default-src="{{ asset('img/admin-avatar.svg') }}" onerror="this.src=this.dataset.defaultSrc">
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h2>
                        <p class="text-gray-600">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700">Foto Profil (opsional)</label>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="mt-1 block w-full text-sm">
                        @error('profile_photo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm
                                      @error('name') border-red-500 @enderror" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm
                                      @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', auth()->user()->phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ubah Password</h3>
                        <p class="text-sm text-gray-600 mb-4">Kosongkan field di bawah jika Anda tidak ingin mengubah password</p>
                        
                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm
                                                  @error('current_password') border-red-500 @enderror">
                                    <button type="button" class="absolute inset-y-0 right-2 px-2 text-gray-600" onclick="togglePassword('current_password', this)" aria-label="Toggle password visibility">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm
                                                  @error('password') border-red-500 @enderror">
                                    <button type="button" class="absolute inset-y-0 right-2 px-2 text-gray-600" onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <button type="button" class="absolute inset-y-0 right-2 px-2 text-gray-600" onclick="togglePassword('password_confirmation', this)" aria-label="Toggle password visibility">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
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