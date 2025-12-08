@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Invoice Booking</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Detail Booking -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Detail Booking</h2>
                <div class="space-y-3 text-sm">
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Acara:</span>
                        <span class="font-semibold">{{ $booking->event_name }}</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Jenis:</span>
                        <span>{{ $booking->event_type }}</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Gedung:</span>
                        <span class="font-semibold">{{ $booking->gedung->nama ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Kapasitas:</span>
                        <span>{{ $booking->capacity }} orang</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Tanggal:</span>
                        <span>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}@if($booking->end_date && $booking->end_date !== $booking->date)<br> s/d {{ \Carbon\Carbon::parse($booking->end_date)->format('d M Y') }}@endif</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Waktu:</span>
                        <span>{{ $booking->start_time }} - {{ $booking->end_time }}</span>
                    </div>
                    <div class="grid grid-cols-2">
                        <span class="text-gray-600">Kontak:</span>
                        <span>{{ $booking->phone ?? ($booking->user->phone ?? '-') }}</span>
                    </div>
                </div>
            </div>

            <!-- Fasilitas -->
            @if($booking->bookingFasilitas && $booking->bookingFasilitas->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Fasilitas Tambahan</h2>
                <div class="space-y-2 text-sm">
                    @foreach($booking->bookingFasilitas as $bf)
                    <div class="flex justify-between py-2 border-b">
                        <span>{{ $bf->fasilitas->nama }} ({{ $bf->jumlah }}x)</span>
                        <span class="font-semibold">Rp {{ number_format(($bf->fasilitas->harga ?? 0) * $bf->jumlah, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Status</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status Booking</p>
                        <span class="px-3 py-1 rounded text-white text-sm font-semibold
                            {{ $booking->status === '2' ? 'bg-green-600' : ($booking->status === '1' ? 'bg-yellow-600' : ($booking->status === '3' ? 'bg-red-600' : 'bg-blue-600')) }}">
                            {{ ['1'=>'Menunggu Persetujuan','2'=>'Disetujui','3'=>'Ditolak','4'=>'Selesai'][$booking->status] ?? $booking->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Status Pembayaran</p>
                        <span class="px-3 py-1 rounded text-white text-sm font-semibold
                            {{ $payment->status === '2' ? 'bg-green-600' : ($payment->status === '0' ? 'bg-gray-600' : ($payment->status === '1' ? 'bg-yellow-600' : 'bg-red-600')) }}">
                            {{ ['0'=>'Belum Dibayar','1'=>'Proses Verifikasi','2'=>'Terbayar','3'=>'Dibatalkan'][$payment->status] ?? $payment->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div>
            <div class="bg-blue-50 rounded-lg shadow p-6 sticky top-20">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pembayaran</h2>
                
                <div class="space-y-3 mb-6 pb-6 border-b">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Biaya Gedung</span>
                        <span class="font-semibold">Rp {{ number_format($payment->amount * 0.7, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->bookingFasilitas && $booking->bookingFasilitas->count() > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Fasilitas</span>
                        <span class="font-semibold">Rp {{ number_format($payment->amount * 0.3, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="bg-white rounded p-4 mb-6">
                    <p class="text-gray-600 text-sm mb-1">Total Pembayaran</p>
                    <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </div>

                @if($payment->status === '0' && $booking->status === '2')
                <div class="space-y-2">
                    <a href="{{ route('payments.upload.form', $payment->id) }}" class="block text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold">
                        💳 Upload Bukti Pembayaran
                    </a>
                    <a href="{{ route('booking.index') }}" class="block text-center border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded">
                        Lihat Booking Lainnya
                    </a>
                </div>
                @elseif($payment->status === '1')
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                    <p class="text-sm text-yellow-800">
                        <strong>⏳ Pembayaran Anda sedang diverifikasi.</strong><br>
                        Admin akan mengecek bukti pembayaran dalam beberapa jam.
                    </p>
                </div>
                @elseif($payment->status === '2')
                <div class="bg-green-50 border border-green-200 rounded p-4">
                    <p class="text-sm text-green-800">
                        <strong>✓ Pembayaran Anda sudah terverifikasi!</strong><br>
                        Booking Anda siap digunakan.
                    </p>
                </div>
                @else
                <a href="{{ route('booking.index') }}" class="block text-center border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded">
                    Kembali
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
