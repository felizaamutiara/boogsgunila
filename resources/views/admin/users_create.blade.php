@extends('admin.layout')

@section('content')
<h1 class="text-2xl font-extrabold text-gray-800 mb-4">Tambah Pengguna</h1>

@if($errors->any())
	<div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
		<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

<div class="bg-white rounded-xl shadow p-6">
	<form method="POST" action="{{ route('admin.users.store') }}">
		@csrf
		<div class="space-y-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
				<input type="text" name="name" value="{{ old('name') }}" required minlength="3" maxlength="255"
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" oninput="validateName(this)">
				<p id="name-error" class="text-red-600 text-sm mt-1 hidden"></p>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
				<input type="email" name="email" value="{{ old('email') }}" required
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" oninput="validateEmail(this)">
				<p id="email-error" class="text-red-600 text-sm mt-1 hidden"></p>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
				<input type="text" name="phone" value="{{ old('phone') }}"
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0812xxxx" oninput="validatePhone(this)">
				<p id="phone-error" class="text-red-600 text-sm mt-1 hidden"></p>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
				<input type="password" name="password" required minlength="6" maxlength="255"
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" oninput="validatePassword(this)">
				<p id="password-error" class="text-red-600 text-sm mt-1 hidden"></p>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
				<select name="role" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
					<option value="">- Pilih Role -</option>
					<option value="U" {{ old('role') === 'U' ? 'selected' : '' }}>User</option>
					<option value="A" {{ old('role') === 'A' ? 'selected' : '' }}>Admin</option>
				</select>
			</div>
			<div class="flex gap-2 pt-4">
				<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
				<a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Batal</a>
			</div>
		</div>
		
		<script>
			function validateName(input) {
				const errorEl = document.getElementById('name-error');
				if (input.value.trim().length < 3) {
					errorEl.textContent = 'Nama minimal 3 karakter';
					errorEl.classList.remove('hidden');
					input.classList.add('border-red-600');
				} else {
					errorEl.classList.add('hidden');
					input.classList.remove('border-red-600');
				}
			}
			
			function validateEmail(input) {
				const errorEl = document.getElementById('email-error');
				const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
				if (input.value && !emailRegex.test(input.value)) {
					errorEl.textContent = 'Format email tidak valid';
					errorEl.classList.remove('hidden');
					input.classList.add('border-red-600');
				} else {
					errorEl.classList.add('hidden');
					input.classList.remove('border-red-600');
				}
			}
			
			function validatePhone(input) {
				const errorEl = document.getElementById('phone-error');
				if (input.value && !/^(\+62|62|0)[0-9]{9,12}$/.test(input.value)) {
					errorEl.textContent = 'Format nomor telepon tidak valid (mulai dengan 0 atau +62)';
					errorEl.classList.remove('hidden');
					input.classList.add('border-red-600');
				} else {
					errorEl.classList.add('hidden');
					input.classList.remove('border-red-600');
				}
			}
			
			function validatePassword(input) {
				const errorEl = document.getElementById('password-error');
				if (input.value.length < 6) {
					errorEl.textContent = 'Password minimal 6 karakter';
					errorEl.classList.remove('hidden');
					input.classList.add('border-red-600');
				} else {
					errorEl.classList.add('hidden');
					input.classList.remove('border-red-600');
				}
			}
		</script>
	</form>
</div>
@endsection

