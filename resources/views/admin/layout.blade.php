<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $title ?? 'Admin • BooGSG' }}</title>
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
		body { font-family: 'Inter', sans-serif; }
	</style>
	@stack('head')
</head>
<body class="bg-gray-100">
	<div class="min-h-screen flex">
		<!-- Sidebar -->
		<aside class="w-64 bg-white border-r">
			<div class="px-6 py-4 border-b">
				<div class="text-xl font-extrabold text-blue-900">BooGSG</div>
				<div class="text-xs text-gray-500">Administrator</div>
			</div>
			<nav class="px-3 py-4 space-y-1 text-sm">
				<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
				<a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('profile') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-user"></i> Profil Saya</a>
				
				<!-- Management Section -->
				<div class="mt-4 pt-3 border-t">
					<div class="px-3 py-1 text-xs font-semibold text-gray-600 uppercase">Manajemen</div>
					<a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('admin.users.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-users"></i> Pengguna</a>
					<a href="{{ route('admin.schedules.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('admin.schedules.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-calendar"></i> Jadwal</a>
					<a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('admin.payments.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-file-invoice-dollar"></i> Pembayaran</a>
					<a href="{{ route('admin.payment_accounts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('admin.payment_accounts.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-credit-card"></i> Metode Pembayaran</a>
				</div>
				
				<!-- Asset Management Section -->
				<div class="mt-4 pt-3 border-t">
					<div class="px-3 py-1 text-xs font-semibold text-gray-600 uppercase">Aset</div>
					<a href="{{ route('gedung.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('gedung.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-building"></i> Gedung</a>
					<a href="{{ route('fasilitas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-blue-50 {{ request()->routeIs('fasilitas.*') ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700' }}"><i class="fa-solid fa-boxes-stacked"></i> Fasilitas</a>
				</div>
				
				<!-- Logout -->
				<form method="POST" action="{{ route('auth.logout') }}" class="mt-4 px-3" id="admin-logout-form">
					@csrf
					<button type="button" onclick="if(confirm('Anda yakin ingin logout?')){ document.getElementById('admin-logout-form').submit(); }" class="w-full flex items-center gap-3 px-3 py-2 rounded-md text-red-600 hover:bg-red-50"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</button>
				</form>
			</nav>
		</aside>

		<!-- Main -->
		<div class="flex-1 flex flex-col">
			<header class="bg-white border-b">
				<div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
					<div class="w-full max-w-xl">
						<form method="GET" action="{{ url()->current() }}">
							<div class="relative">
								<i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
								<input type="text" name="q" value="{{ request('q') }}" placeholder="Cari..." class="w-full pl-10 pr-3 py-2 border rounded-lg focus:ring-1 focus:ring-blue-500">
							</div>
						</form>
					</div>
					@php
						// Prefer user's uploaded profile photo, otherwise show an admin-specific default avatar
						if (!empty(auth()->user()->profile_photo_url)) {
							$avatarUrl = auth()->user()->profile_photo_url;
						} else {
							$avatarUrl = (auth()->user()->role === 'A') ? asset('img/admin-avatar.svg') : asset('img/default-avatar.png');
						}
					@endphp
					<div class="flex items-center gap-3">
						<div class="text-right">
							<div class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'Admin' }}</div>
							<div class="text-xs text-gray-500">Administrator</div>
						</div>
						<a href="{{ route('profile') }}" title="Profil Saya">
							<img src="{{ $avatarUrl }}"
						 	 class="w-9 h-9 rounded-full border object-cover"
						 	 alt="avatar"
						 	 data-default-avatar="{{ asset('img/default-avatar.png') }}"
						 	 onerror="this.src=this.dataset.defaultAvatar">
						</a>
					</div>
				</div>
			</header>

			<main class="flex-1 p-6">
				@yield('content')
			</main>
		</div>
	</div>

	<!-- Flash toast for admin actions -->
	<div id="admin-flash-toast" class="fixed top-6 right-6 z-50 hidden">
		<div id="admin-flash-inner" class="max-w-sm bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg"></div>
	</div>

	@stack('scripts')

	<!-- flash data container (no inline blade in JS) -->
	<div id="admin-flash-data" data-success="{{ e(session('success')) }}" data-error="{{ e(session('error')) }}"></div>

	<script>
		(function(){
			var toast = document.getElementById('admin-flash-toast');
			var inner = document.getElementById('admin-flash-inner');
			var data = document.getElementById('admin-flash-data');
			if (!data) return;
			var msg = data.dataset.success || data.dataset.error || '';
			var isError = !!data.dataset.error && !data.dataset.success;
			if (!msg) return;
			inner.textContent = msg;
			if (isError) {
				inner.classList.remove('bg-green-600');
				inner.classList.add('bg-red-600');
			}
			toast.classList.remove('hidden');
			setTimeout(function(){ toast.classList.add('opacity-100'); }, 10);
			setTimeout(function(){ toast.classList.add('hidden'); }, 4000);
		})();
	</script>
</body>
<html>


