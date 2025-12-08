@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-700 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl md:text-5xl font-bold mb-2">Tentang BooSG Unila</h1>
        <p class="text-blue-100 text-lg">Transformasi digital untuk layanan penyewaan Gedung Serba Guna Universitas Lampung.</p>
    </div>
</section>

<!-- Main Content -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8 items-center mb-16">
            <!-- Left: Image -->
            <div class="rounded-xl overflow-hidden shadow-lg">
                <img src="{{ asset('img/gsg.png') }}" alt="Gedung Serba Guna" class="w-full h-auto object-cover" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
            </div>
            
            <!-- Right: Content -->
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Apa itu BooSG Unila?</h2>
                <p class="text-gray-600 text-lg leading-relaxed mb-4">
                    <strong>BooSG Unila</strong> adalah sistem digital yang dirancang untuk mempermudah proses pemesanan dan penyewaan <strong>Gedung Serba Guna (GSG)</strong> Universitas Lampung. Melalui sistem ini, pengguna dapat melihat jadwal ketersediaan fasilitas, mengajukan pemesanan acara dengan transparan.
                </p>
                <p class="text-gray-600 text-lg leading-relaxed">
                    Kami hadir sebagai solusi modern yang mendukung efisiensi, akurasi data, dan kemudahan civitas akademika dalam mengadakan berbagai kegiatan di lingkungan kampus.
                </p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 text-center">Fitur Unggulan</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1: Online Booking -->
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-lg transition duration-300 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-blue-100 rounded-full p-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pemesanan Online</h3>
                    <p class="text-gray-600">
                        Reservasi GSG digital dapat dilakukan dengan kapan saja melalui sistem. Daftar pengajuan booking saya meliputi informasi lengkap tentang acara Anda.
                    </p>
                </div>

                <!-- Feature 2: Real-time Schedule -->
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-lg transition duration-300 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-blue-100 rounded-full p-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Jadwal Real-time</h3>
                    <p class="text-gray-600">
                        Delta ketersediaan jadwal diperbaharui secara langsung memungkinkan pengguna dapat melihat waktu terbaru ketersediaan fasilitas untuk setiap acara.
                    </p>
                </div>

                <!-- Feature 3: Secure Transaction -->
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-lg transition duration-300 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="bg-blue-100 rounded-full p-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Transaksi Aman</h3>
                    <p class="text-gray-600">
                        Sistem pembayaran dan konfirmasi dijamin aman dengan verifikasi data penggunaan terintegrasi yang aman dan terpercaya.
                    </p>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="bg-blue-600 rounded-xl shadow-lg p-8 md:p-12 text-center text-white">
            <h2 class="text-3xl font-bold mb-4">Mempermudah Akses Kegiatan Kampus</h2>
            <p class="text-blue-100 text-lg mb-8 max-w-3xl mx-auto">
                Dengan BooSG Unila, semua urusan penyewaan GSG kini lebih efisien, transparan, dan praktis. Cukup beberapa klik, acara Anda siap terlaksanakan!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('booking.create') }}" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition duration-300">
                    Mulai Sewa Sekarang
                </a>
                <a href="{{ route('home') }}" class="inline-block bg-blue-700 border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-800 transition duration-300">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Additional Info Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Mengapa Memilih BooSG Unila?</h2>
        
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Reason 1 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Proses Cepat dan Mudah</h3>
                    <p class="mt-2 text-gray-600">Booking GSG dapat diselesaikan dalam hitungan menit tanpa perlu datang langsung ke kantor.</p>
                </div>
            </div>

            <!-- Reason 2 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Transparansi Penuh</h3>
                    <p class="mt-2 text-gray-600">Lihat status booking Anda secara real-time dan terima notifikasi update langsung di aplikasi.</p>
                </div>
            </div>

            <!-- Reason 3 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Harga Kompetitif</h3>
                    <p class="mt-2 text-gray-600">Dapatkan penawaran terbaik dengan sistem perhitungan yang jelas dan transparan.</p>
                </div>
            </div>

            <!-- Reason 4 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-600 text-white">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Dukungan 24/7</h3>
                    <p class="mt-2 text-gray-600">Tim support kami siap membantu menjawab pertanyaan dan menyelesaikan masalah Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
