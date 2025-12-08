@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Booking Saya</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div></div>
        <a href="{{ route('booking.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">+ Buat Booking Baru</a>
    </div>

    <div class="space-y-4">
        @forelse($items as $booking)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Left: Main Info -->
                    <div class="md:col-span-2">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-800">{{ $booking->event_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $booking->event_type }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Gedung</p>
                                <p class="font-semibold text-gray-900">{{ $booking->gedung->nama ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Tanggal</p>
                                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}@if($booking->end_date && $booking->end_date !== $booking->date) - {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}@endif</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Waktu</p>
                                <p class="font-semibold text-gray-900">{{ $booking->start_time }} - {{ $booking->end_time }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Kapasitas</p>
                                <p class="font-semibold text-gray-900">{{ $booking->capacity }} orang</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Status & Actions -->
                    <div class="flex flex-col justify-between">
                        <div>
                            <p class="text-gray-600 text-xs mb-2">Status</p>
                            <span class="px-3 py-1 rounded text-white text-sm font-semibold
                                {{ $booking->status === '2' ? 'bg-green-600' : ($booking->status === '1' ? 'bg-yellow-600' : ($booking->status === '3' ? 'bg-red-600' : 'bg-blue-600')) }}">
                                {{ ['1'=>'Menunggu','2'=>'Disetujui','3'=>'Ditolak','4'=>'Selesai'][$booking->status] ?? $booking->status }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-2 mt-4">
                            <a href="{{ route('booking.invoice', $booking->id) }}" class="text-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm">Lihat Invoice</a>
                            
                            @if($booking->status === '1')
                                <a href="{{ route('booking.edit', $booking->id) }}" class="text-center bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded text-sm">Edit</a>
                                <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Batalkan booking ini?')">
                                    @csrf @method('PUT')
                                    <button class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">Batalkan</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-gray-600 mb-4">Belum ada booking. Mulai buat booking baru sekarang!</p>
                <a href="{{ route('booking.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Buat Booking Sekarang</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
