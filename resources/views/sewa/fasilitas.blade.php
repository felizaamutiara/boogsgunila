@extends('layouts.app')

@php
	$fasilitas = \App\Models\Fasilitas::all();
@endphp

@section('content')
<section class="bg-gradient-to-b from-blue-50 to-white">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
		<!-- Breadcrumb -->
		<nav class="text-sm text-gray-500">
			<a href="{{ route('home') }}" class="hover:text-blue-700">Beranda</a>
			<span class="mx-2">/</span>
			<span class="text-blue-800 font-medium">Fasilitas</span>
		</nav>
		<h1 class="mt-2 text-3xl md:text-4xl font-extrabold text-blue-900">Fasilitas GSG</h1>
		<p class="text-gray-600">Daftar fasilitas yang tersedia untuk mendukung acara Anda.</p>

		<div class="grid md:grid-cols-3 gap-8 mt-6">
			<div class="md:col-span-2 grid md:grid-cols-3 gap-4">
				<div class="md:col-span-3 relative">
					<img src="{{ asset('img/wisuda.jpg') }}" alt="Full Set Dekor Wisuda" class="w-full h-64 md:h-80 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1519167758993-4d5957b101df?w=800&h=600&fit=crop'">
					<span class="absolute bottom-3 left-3 bg-white/90 text-gray-800 text-xs px-3 py-1 rounded-full shadow">Paket lengkap dekorasi</span>
				</div>
				<img src="{{ asset('img/konser.jpeg') }}" alt="Galeri 1" class="w-full h-40 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=400&h=300&fit=crop'">
				<img src="{{ asset('img/workshop.jpg') }}" alt="Galeri 2" class="w-full h-40 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?w=400&h=300&fit=crop'">
				<img src="{{ asset('img/wisuda.jpeg') }}" alt="Galeri 3" class="w-full h-40 object-cover rounded-2xl shadow-md" onerror="this.src='https://images.unsplash.com/photo-1519157259-50b8d405f718?w=400&h=300&fit=crop'">>
			</div>

			<aside class="bg-white border rounded-2xl p-6 h-max sticky top-24 shadow-sm">
				<h3 class="text-lg font-semibold text-gray-800">Mulai Sewa</h3>
				<p class="mt-1 text-3xl font-extrabold text-blue-600">150K <span class="text-gray-500 text-base font-medium">per Hari</span></p>
				<a href="{{ url('/booking/create') }}" class="mt-4 inline-block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg transition">Sewa Sekarang</a>
				<a href="{{ route('public.jadwal') }}" class="mt-2 inline-block w-full text-center bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 rounded-lg transition">Lihat Jadwal Booking</a>
				
				<div class="mt-6 border-t pt-4">
					<p class="text-sm font-medium text-gray-700 mb-2">Termasuk</p>
					<ul class="space-y-2 text-sm text-gray-600">
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> Dekor bunga & backdrop</li>
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> Meja, kursi & karpet merah</li>
						<li class="flex items-center"><i class="fas fa-check text-green-600 mr-2"></i> Penataan panggung rapi</li>
					</ul>
				</div>
			</aside>
		</div>

		<div class="mt-8 bg-white rounded-2xl border p-6 leading-relaxed text-gray-700 shadow-sm">
			<h2 class="text-xl font-bold text-blue-900 mb-2">Informasi Penyewaan</h2>
			<p>
				Paket dekorasi lengkap untuk wisuda di Gedung Serba Guna (GSG) Unila. Paket menghadirkan suasana megah dan berkesan dengan perlengkapan seperti meja depan, sound system, TV proyektor, karpet merah, kursi tambahan, dan bunga-bunga dekoratif yang mempercantik area panggung serta ruangan.
			</p>
		</div>

		<!-- Fasilitas Details Section -->
		@if($fasilitas->count() > 0)
		<div class="mt-12">
			<h2 class="text-2xl font-bold text-blue-900 mb-6">Detail Fasilitas Tersedia</h2>
			<div class="space-y-4">
				@foreach($fasilitas as $item)
				<div class="bg-white border rounded-xl shadow-sm hover:shadow-md transition overflow-hidden">
					<!-- Header Accordion -->
					<button type="button" onclick="toggleAccordion(this)" class="w-full flex items-center justify-between p-5 hover:bg-gray-50 transition">
						<div class="flex items-center gap-4 text-left flex-1">
						<div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0 overflow-hidden">
							@if($item->image && file_exists(public_path('img/fasilitas/' . $item->image)))
								<img src="{{ asset('img/fasilitas/' . $item->image) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover">
							@else
								<img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=100&h=100&fit=crop" alt="{{ $item->nama }}" class="w-full h-full object-cover">
							@endif
						</div>
							<div>
								<h3 class="font-semibold text-gray-800 text-lg">{{ $item->nama }}</h3>
								<p class="text-blue-600 font-bold">Rp {{ number_format($item->harga, 0, ',', '.') }} <span class="text-sm text-gray-600">/item</span></p>
							</div>
						</div>
						<i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
					</button>

					<!-- Content Accordion -->
					<div class="hidden" style="max-height: 0; overflow: hidden; transition: max-height 0.3s ease;">
						<div class="px-5 py-4 border-t bg-gray-50">
							<div class="grid md:grid-cols-2 gap-6">
							<!-- Image -->
							<div>
								@if($item->image && file_exists(public_path('img/fasilitas/' . $item->image)))
									<img src="{{ asset('img/fasilitas/' . $item->image) }}" alt="{{ $item->nama }}" class="w-full h-64 object-cover rounded-lg shadow">
								@else
									<img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=500&fit=crop" alt="{{ $item->nama }}" class="w-full h-64 object-cover rounded-lg shadow">
								@endif
							</div>								<!-- Details -->
								<div>
									<h4 class="font-bold text-gray-900 mb-3">Deskripsi</h4>
									<p class="text-gray-700 mb-4 leading-relaxed">{{ $item->deskripsi ?? 'Tidak ada deskripsi' }}</p>

									<div class="bg-white rounded-lg p-4 mb-4 border border-gray-200">
										<div class="flex justify-between items-center mb-3">
											<span class="text-gray-600 font-medium">Stok Tersedia</span>
											<span class="text-2xl font-bold text-blue-600">{{ $item->stok }} unit</span>
										</div>
										<div class="flex justify-between items-center">
											<span class="text-gray-600 font-medium">Harga</span>
											<span class="text-2xl font-bold text-green-600">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
										</div>
									</div>

									<button onclick="addToBooking('{{ $item->id }}', '{{ $item->nama }}')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
										<i class="fas fa-plus mr-2"></i> Tambah ke Pesanan
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				@endforeach
			</div>
		</div>
		@endif
	</div>
</section>

<script>
	function toggleAccordion(button) {
		const icon = button.querySelector('i');
		const content = button.nextElementSibling;
		const isOpen = !content.classList.contains('hidden');

		// Close all other accordions
		document.querySelectorAll('[onclick="toggleAccordion(this)"]').forEach(btn => {
			if (btn !== button) {
				const otherContent = btn.nextElementSibling;
				const otherIcon = btn.querySelector('i');
				if (otherContent && otherIcon) {
					otherContent.classList.add('hidden');
					otherContent.style.maxHeight = '0px';
					otherIcon.style.transform = 'rotate(0deg)';
				}
			}
		});

		// Toggle current accordion
		if (isOpen) {
			content.classList.add('hidden');
			content.style.maxHeight = '0px';
			icon.style.transform = 'rotate(0deg)';
		} else {
			content.classList.remove('hidden');
			setTimeout(() => {
				content.style.maxHeight = content.scrollHeight + 'px';
			}, 10);
			icon.style.transform = 'rotate(180deg)';
		}
	}

	function addToBooking(fasilitasId, fasilitasNama) {
		window.location.href = '{{ url("/booking/create") }}';
	}
</script>
@endsection


