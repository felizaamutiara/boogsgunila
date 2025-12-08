@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-white py-12 md:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <!-- Left Side - Text and CTA -->
            <div>
                <h1 class="text-4xl md:text-5xl font-bold text-blue-900 mb-4">
                    Booking GSG Lebih Mudah, Cepat, dan Terorganisir.
                </h1>
                <p class="text-gray-600 text-lg mb-6">
                    Tempat terbaik untuk reservasi acara kampus tanpa ribet tidak menghabiskan waktu.
                </p>
                <a href="{{ route('booking.create') }}" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-600 transition duration-300 mb-8">
                    Lebih Lanjut
                </a>
                
                <!-- Statistics -->
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-desktop text-xl"></i>
                        <span class="text-sm">2500 Users</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-camera text-xl"></i>
                        <span class="text-sm">200 treasure</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-map-marker-alt text-xl"></i>
                        <span class="text-sm">1000 viewer</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Image -->
            <div class="hidden md:block">
                <div class="relative">
                    <img src="{{ asset('img/gsg.png') }}" alt="Gedung Serba Guna Universitas Lampung" class="rounded-lg shadow-lg w-full h-auto max-h-96 object-cover" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Action Buttons Section -->
<section class="bg-gradient-to-r from-blue-50 to-blue-100 py-8 border-b border-blue-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-4">
            <a href="{{ route('booking.create') }}" class="bg-white hover:shadow-lg border-l-4 border-blue-600 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-plus text-xl text-blue-600"></i>
                <div class="text-left">
                    <span class="font-medium text-gray-800 text-sm block">Buat Booking</span>
                    <span class="text-xs text-gray-500">Mulai booking sekarang</span>
                </div>
            </a>
            <a href="{{ route('booking.index') }}" class="bg-white hover:shadow-lg border-l-4 border-green-600 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-calendar-alt text-xl text-green-600"></i>
                <div class="text-left">
                    <span class="font-medium text-gray-800 text-sm block">Booking Saya</span>
                    <span class="text-xs text-gray-500">{{ $stats['total_bookings'] ?? 0 }} jadwal</span>
                </div>
            </a>
            <a href="{{ route('payments.index') }}" class="bg-white hover:shadow-lg border-l-4 border-yellow-600 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-money-bill text-xl text-yellow-600"></i>
                <div class="text-left">
                    <span class="font-medium text-gray-800 text-sm block">Pembayaran</span>
                    <span class="text-xs text-gray-500">Status pembayaran</span>
                </div>
            </a>
            <a href="{{ route('public.jadwal') }}" class="bg-white hover:shadow-lg border-l-4 border-purple-600 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-list text-xl text-purple-600"></i>
                <div class="text-left">
                    <span class="font-medium text-gray-800 text-sm block">Jadwal Publik</span>
                    <span class="text-xs text-gray-500">Lihat jadwal lainnya</span>
                </div>
            </a>
        </div>
    </div>
</section>

@php
function facilityImage($name) {
    $exts = ['jpg', 'jpeg', 'png'];
    foreach ($exts as $ext) {
        $path = public_path("img/fasilitas/{$name}.{$ext}");
        if (file_exists($path)) {
            return asset("img/fasilitas/{$name}.{$ext}");
        }
    }
    return asset('img/placeholder.jpg'); // fallback jika tidak ada
}

$fasilitasList = [
    ['nama' => 'Dekor Podium', 'harga' => 50000],
    ['nama' => 'Dekor Wisuda', 'harga' => 75000],
    ['nama' => 'Elektronik', 'harga' => 25000],
    ['nama' => 'Kursi', 'harga' => 15000],
    ['nama' => 'Podium Biasa', 'harga' => 100000],
    ['nama' => 'Lampu', 'harga' => 50000],
];

$fasilitasList = array_slice($fasilitasList, 0, 6);
@endphp

<section class="py-12 bg-white" id="sewa">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-900 mb-8">Fasilitas GSG</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($fasilitasList as $facility)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <div class="relative">
                    <img 
                        src="{{ facilityImage($facility['nama']) }}" 
                        alt="{{ $facility['nama'] }}" 
                        class="w-full h-48 object-cover">
                    
                    <div class="absolute top-2 right-2 bg-blue-900 bg-opacity-75 text-white px-3 py-1 rounded text-sm font-semibold">
                        {{ number_format($facility['harga'] / 1000, 0) }}k perhari
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">{{ $facility['nama'] }}</h3>
                    <p class="text-gray-600 text-sm">Deskripsi singkat fasilitas</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Penyewaan GSG Section -->
<section class="py-12 bg-gray-50" id="jadwal">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-900 mb-8">Penyewaan GSG</h2>
        <div class="grid md:grid-cols-4 gap-6">
            <!-- Wisuda Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <img src="{{ asset('img/wisuda.jpg') }}" alt="Wisuda" class="w-full h-48 object-cover rounded-t-lg" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Wisuda</h3>
                    <p class="text-gray-600 text-sm">Wisuda Universitas lainnya</p>
                </div>
            </div>
            
            <!-- Konser Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <img src="{{ asset('img/konser.jpeg') }}" alt="Konser" class="w-full h-48 object-cover rounded-t-lg" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Konser</h3>
                    <p class="text-gray-600 text-sm">Pengadaan Konser</p>
                </div>
            </div>
            
            <!-- Workshop Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <img src="{{ asset('img/workshop.jpg') }}" alt="Workshop" class="w-full h-48 object-cover rounded-t-lg" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Workshop</h3>
                    <p class="text-gray-600 text-sm">Seminar ataupun Workshop</p>
                </div>
            </div>
            
            <!-- Acara Kampus Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                <img src="{{ asset('img/acarahima.jpeg') }}" alt="Acara Kampus" class="w-full h-48 object-cover rounded-t-lg" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Acara Kampus</h3>
                    <p class="text-gray-600 text-sm">Acara Hima, UKM, dan lainnya</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
