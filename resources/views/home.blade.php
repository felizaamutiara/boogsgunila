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
                <a href="{{ route('public.sewa.fasilitas') }}" class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-600 transition duration-300 mb-8">
                    Lebih Lanjut
                </a>
                
                <!-- Statistics -->
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-shopping-bag text-xl"></i>
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
<section class="bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-4">
            <a href="#jadwal" class="bg-blue-100 hover:bg-blue-200 text-gray-700 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-calendar-alt text-xl"></i>
                <span class="font-medium">Lihat Jadwal</span>
            </a>
            <a href="{{ route('public.sewa.fasilitas') }}" class="bg-blue-100 hover:bg-blue-200 text-gray-700 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-user text-xl"></i>
                <span class="font-medium">Fasilitas</span>
            </a>
            <a href="{{ route('public.sewa.gedung') }}" class="bg-blue-100 hover:bg-blue-200 text-gray-700 px-6 py-4 rounded-lg flex items-center gap-3 transition duration-300">
                <i class="fas fa-map-marker-alt text-xl"></i>
                <span class="font-medium">Lokasi GSG</span>
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

// Ambil 5 fasilitas pertama saja
$fasilitasList = array_slice($fasilitasList, 0, 6);
@endphp

<section class="py-12 bg-white" id="sewa">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-blue-900 mb-8">Fasilitas GSG</h2>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($fasilitasList as $facility)
            <a href="{{ route('public.sewa.fasilitas') }}" class="block bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
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
            </a>
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
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 relative">
                <img src="{{ asset('img/wisuda.jpg') }}" alt="Wisuda" class="w-full h-48 object-cover" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="absolute top-2 right-2 bg-green-700 text-white px-3 py-1 rounded text-sm font-semibold">
                    Rp {{ number_format(500000,0,',','.') }}/jam
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Wisuda</h3>
                    <p class="text-gray-600 text-sm">Wisuda Universitas lainnya</p>
                </div>
            </div>
            
            <!-- Konser Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 relative">
                <img src="{{ asset('img/konser.jpeg') }}" alt="Konser" class="w-full h-48 object-cover" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="absolute top-2 right-2 bg-green-700 text-white px-3 py-1 rounded text-sm font-semibold">
                    Rp {{ number_format(500000,0,',','.') }}/jam
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Konser</h3>
                    <p class="text-gray-600 text-sm">Pengadaan Konser</p>
                </div>
            </div>
            
            <!-- Workshop Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 relative">
                <img src="{{ asset('img/workshop.jpg') }}" alt="Workshop" class="w-full h-48 object-cover" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="absolute top-2 right-2 bg-green-700 text-white px-3 py-1 rounded text-sm font-semibold">
                    Rp {{ number_format(500000,0,',','.') }}/jam
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Workshop</h3>
                    <p class="text-gray-600 text-sm">Seminar ataupun Workshop</p>
                </div>
            </div>
            
            <!-- Acara Kampus Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 relative">
                <img src="{{ asset('img/acarahima.jpeg') }}" alt="Acara Kampus" class="w-full h-48 object-cover" onerror="this.src='{{ asset('img/placeholder.jpg') }}'">
                <div class="absolute top-2 right-2 bg-green-700 text-white px-3 py-1 rounded text-sm font-semibold">
                    Rp {{ number_format(500000,0,',','.') }}/jam
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-blue-900 text-lg mb-1">Acara Kampus</h3>
                    <p class="text-gray-600 text-sm">Acara Hima, UKM, dan lainnya</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Form Section (Hidden by default, can be shown via anchor) -->
<section class="py-12 bg-white hidden" id="booking-form">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-2 bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Cari Jadwal Tersedia</h2>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input id="date" type="date" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Mulai</label>
                        <input id="start_time" type="time" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Selesai</label>
                        <input id="end_time" type="time" class="mt-1 w-full border rounded px-3 py-2" />
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="font-semibold mb-2">Fasilitas Tambahan</h3>
                    <div class="grid md:grid-cols-2 gap-3">
                        @foreach($facilities as $f)
                            <label class="flex items-center gap-3 bg-gray-50 border rounded px-3 py-2">
                                <input type="checkbox" class="facility" data-id="{{ $f->id }}" />
                                <span class="flex-1">{{ $f->nama }}</span>
                                <span class="text-sm text-gray-600">Rp {{ number_format($f->harga,0,',','.') }}</span>
                                <input type="number" class="facility-qty w-20 border rounded px-2 py-1" min="1" value="1" disabled />
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <button id="checkBtn" class="px-4 py-2 bg-indigo-600 text-white rounded">Cek Ketersediaan</button>
                    <a id="bookBtn" href="#" class="px-4 py-2 bg-green-600 text-white rounded hidden">Lanjutkan Booking</a>
                    <span id="statusText" class="text-sm"></span>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold mb-4">Estimasi Biaya</h2>
                <div id="quoteBox" class="space-y-2 text-gray-800">
                    <div class="flex justify-between"><span>Durasi</span><span id="q_hours">-</span></div>
                    <div class="flex justify-between"><span>Tarif Dasar</span><span id="q_base">-</span></div>
                    <div class="flex justify-between"><span>Fasilitas</span><span id="q_fac">-</span></div>
                    <hr />
                    <div class="flex justify-between font-bold text-lg"><span>Total</span><span id="q_total">-</span></div>
                </div>
                <p class="mt-3 text-xs text-gray-500">Harga estimasi. Total final muncul saat membuat booking.</p>
            </div>
        </div>
    </div>
</section>

<script>
    // Show booking form when clicking "Lihat Jadwal"
    document.querySelectorAll('a[href="#jadwal"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const formSection = document.getElementById('booking-form');
            formSection.classList.remove('hidden');
            formSection.scrollIntoView({ behavior: 'smooth' });
        });
    });

    document.querySelectorAll('.facility').forEach((cb) => {
        cb.addEventListener('change', (e) => {
            const qty = e.target.parentElement.querySelector('.facility-qty');
            qty.disabled = !e.target.checked;
        });
    });

    const toCurrency = (n) => 'Rp ' + (Math.round(n)).toLocaleString('id-ID');

    async function fetchAvailability() {
        const date = document.getElementById('date').value;
        const start = document.getElementById('start_time').value;
        const end = document.getElementById('end_time').value;
        if (!date || !start || !end) return { ok:false };
        const res = await fetch(`{{ route('public.availability') }}?date=${date}&start_time=${start}&end_time=${end}`);
        return res.json();
    }
    
    async function fetchQuote() {
        const date = document.getElementById('date').value;
        const start = document.getElementById('start_time').value;
        const end = document.getElementById('end_time').value;
        const facilities = [];
        document.querySelectorAll('.facility').forEach((cb) => {
            if (cb.checked) {
                const qty = cb.parentElement.querySelector('.facility-qty').value || 1;
                facilities.push({ id: cb.dataset.id, qty });
            }
        });
        const params = new URLSearchParams({ date, start_time: start, end_time: end });
        facilities.forEach((f, i) => {
            params.append(`facilities[${i}][id]`, f.id);
            params.append(`facilities[${i}][qty]`, f.qty);
        });
        const res = await fetch(`{{ route('public.quote') }}?` + params.toString());
        return res.json();
    }

    const checkBtn = document.getElementById('checkBtn');
    if (checkBtn) {
        checkBtn.addEventListener('click', async () => {
            const statusText = document.getElementById('statusText');
            const bookBtn = document.getElementById('bookBtn');
            statusText.textContent = 'Mengecek...';
            bookBtn.classList.add('hidden');
            const avail = await fetchAvailability();
            const quote = await fetchQuote();
            if (avail.available) {
                statusText.textContent = 'Tersedia';
                bookBtn.classList.remove('hidden');
                const date = document.getElementById('date').value;
                const start = document.getElementById('start_time').value;
                const end = document.getElementById('end_time').value;
                const url = new URL(`{{ url('/booking/create') }}`, window.location.origin);
                url.searchParams.set('date', date);
                url.searchParams.set('start_time', start);
                url.searchParams.set('end_time', end);
                bookBtn.href = url.toString();
            } else {
                statusText.textContent = 'Tidak tersedia pada waktu tersebut.';
            }
            if (quote) {
                document.getElementById('q_hours').textContent = quote.hours + ' jam';
                document.getElementById('q_base').textContent = toCurrency(quote.base);
                document.getElementById('q_fac').textContent = toCurrency(quote.facilities);
                document.getElementById('q_total').textContent = toCurrency(quote.total);
            }
        });
    }
</script>
@endsection
