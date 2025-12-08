@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-b from-blue-50 to-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
		<!-- Breadcrumb -->
		<nav class="text-sm text-gray-500">
			<a href="{{ route('home') }}" class="hover:text-blue-700">Beranda</a>
			<span class="mx-2">/</span>
			<span class="text-blue-800 font-medium">Sewa Gedung</span>
		</nav>
		<h1 class="mt-2 text-3xl md:text-4xl font-extrabold text-blue-900">Gedung Serba Guna Unila</h1>
		<p class="text-gray-600">Fasilitas lengkap untuk berbagai jenis acara dan kegiatan</p>

		<div class="grid md:grid-cols-3 gap-8 mt-6">
			<div class="md:col-span-2 grid md:grid-cols-2 gap-4">
				<div class="md:col-span-2 relative">
					<img src="{{ asset('img/gsg.png') }}" alt="Gedung Serba Guna Unila" class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1519167758993-4d5957b101df?w=800&h=600&fit=crop'">
					<span class="absolute bottom-3 left-3 bg-white/90 text-gray-800 text-xs px-3 py-1 rounded-full shadow">Kapasitas hingga 500 orang</span>
				</div>
				<img src="{{ asset('img/wisuda.jpg') }}" alt="Galeri 1" class="w-full h-40 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?w=400&h=300&fit=crop'">
				<img src="{{ asset('img/konser.jpeg') }}" alt="Galeri 2" class="w-full h-40 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400&h=300&fit=crop'">
			</div>

			<aside class="bg-white border rounded-2xl p-6 h-max sticky top-24 shadow-sm">
				<h3 class="text-lg font-semibold text-gray-800">Mulai Sewa</h3>
				<p class="mt-1 text-3xl font-extrabold text-blue-600">500K <span class="text-gray-500 text-base font-medium">per Jam</span></p>
				<a href="{{ url('/booking/create') }}" class="mt-4 inline-block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition">Sewa Sekarang</a>
				<a href="{{ route('public.jadwal') }}" class="mt-2 inline-block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded-lg transition">Lihat Jadwal Booking</a>
				
				<div class="mt-6 border-t pt-4">
					<p class="text-sm font-medium text-gray-700 mb-2">Fasilitas Tersedia</p>
					<ul class="space-y-2 text-sm text-gray-600">
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> Ruang utama 500 m²</li>
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> Sound system profesional</li>
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> AC dan ventilasi modern</li>
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> Parkir luas</li>
					</ul>
				</div>
			</aside>
		</div>

		<div class="mt-8 bg-white rounded-2xl border p-6 leading-relaxed text-gray-700 shadow-sm">
			<h2 class="text-xl font-bold text-blue-900 mb-2">Informasi Penyewaan</h2>
			<p>
				Gedung Serba Guna Universitas Lampung (GSG Unila) adalah fasilitas modern dengan kapasitas hingga 500 orang. Gedung ini cocok untuk berbagai jenis acara seperti seminar, workshop, konferensi, wisuda, konser, dan acara-acara besar lainnya. Dilengkapi dengan fasilitas lengkap termasuk sound system profesional, AC, parkir yang luas, dan ruang loading yang memadai.
			</p>
		</div>

		<!-- Facilities Grid -->
		<div class="mt-10">
			<h2 class="text-2xl font-bold text-blue-900 mb-6">Fasilitas Tambahan yang Dapat Disewa</h2>
			<div class="grid md:grid-cols-3 gap-6">
			<a href="{{ route('public.sewa.fasilitas') }}" class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition group">
				<div class="h-40 bg-gray-200 overflow-hidden relative">
					<img src="{{ asset('img/wisuda.jpg') }}" alt="Dekorasi" class="w-full h-full object-cover group-hover:scale-105 transition" onerror="this.src='https://images.unsplash.com/photo-1519167758993-4d5957b101df?w=400&h=300&fit=crop'">
				</div>
					<div class="p-4">
						<h3 class="font-semibold text-gray-800 text-lg">Dekorasi Wisuda</h3>
						<p class="text-sm text-gray-600 mt-1">Paket lengkap dekor dengan bunga dan backdrop</p>
						<p class="mt-3 text-blue-600 font-bold">150K per hari</p>
						<button class="mt-3 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded text-sm transition">Lihat Detail</button>
					</div>
				</a>

			<div class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition">
				<div class="h-40 bg-gray-200 overflow-hidden relative">
					<img src="{{ asset('img/konser.jpeg') }}" alt="Sound System" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400&h=300&fit=crop'">
				</div>
					<div class="p-4">
						<h3 class="font-semibold text-gray-800 text-lg">Sound System Premium</h3>
						<p class="text-sm text-gray-600 mt-1">Sistem audio profesional dengan operator</p>
						<p class="mt-3 text-blue-600 font-bold">Hubungi Admin</p>
						<button class="mt-3 w-full bg-gray-400 cursor-not-allowed text-white py-2 rounded text-sm" disabled>Coming Soon</button>
					</div>
				</div>

			<div class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition">
				<div class="h-40 bg-gray-200 overflow-hidden relative">
					<img src="{{ asset('img/workshop.jpg') }}" alt="Catering" class="w-full h-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1555939594-58d7cb561404?w=400&h=300&fit=crop'">
				</div>
					<div class="p-4">
						<h3 class="font-semibold text-gray-800 text-lg">Layanan Catering</h3>
						<p class="text-sm text-gray-600 mt-1">Paket makanan dan minuman untuk acara</p>
						<p class="mt-3 text-blue-600 font-bold">Hubungi Admin</p>
						<button class="mt-3 w-full bg-gray-400 cursor-not-allowed text-white py-2 rounded text-sm" disabled>Coming Soon</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
