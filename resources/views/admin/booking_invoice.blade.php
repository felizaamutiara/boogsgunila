@extends('admin.layout')

@section('content')
<div class="mb-6">
  <h1 class="text-2xl font-bold">Invoice Booking (Admin)</h1>
</div>

<div class="bg-white rounded-xl shadow p-6">
  @if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
  @endif

  <div class="space-y-2">
    <div><strong>Pemesan:</strong> {{ $booking->user->name ?? '-' }}</div>
    <div><strong>Gedung:</strong> {{ $booking->gedung->nama ?? '-' }}</div>
    <div><strong>Acara:</strong> {{ $booking->event_name }} ({{ $booking->event_type }})</div>
    <div><strong>Kapasitas:</strong> {{ $booking->capacity }} orang</div>
    <div><strong>Tanggal:</strong> {{ $booking->date }}@if($booking->end_date && $booking->end_date !== $booking->date) - {{ $booking->end_date }}@endif</div>
    <div><strong>Waktu:</strong> {{ $booking->start_time }} - {{ $booking->end_time }}</div>
    <div><strong>Status Booking:</strong>
      <span class="px-2 py-1 rounded text-white text-xs
        {{ $booking->status==='2' ? 'bg-green-600' : ($booking->status==='1' ? 'bg-yellow-600' : ($booking->status==='3' ? 'bg-red-600' : 'bg-blue-600')) }}">
        {{ ['1'=>'Menunggu','2'=>'Disetujui','3'=>'Ditolak','4'=>'Selesai'][$booking->status] ?? $booking->status }}
      </span>
    </div>
  </div>

  @if($booking->bookingFasilitas && $booking->bookingFasilitas->count() > 0)
  <hr class="my-4">
  <div>
    <strong>Fasilitas yang Disewa:</strong>
    <ul class="list-disc list-inside mt-2 space-y-1">
      @foreach($booking->bookingFasilitas as $bf)
        <li>{{ $bf->fasilitas->nama ?? '-' }} ({{ $bf->jumlah }}x) - Rp {{ number_format(($bf->fasilitas->harga ?? 0) * $bf->jumlah, 0, ',', '.') }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <hr class="my-4">
  <div class="space-y-2">
    <div><strong>Total Pembayaran:</strong> <span class="text-xl font-bold text-blue-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
    <div><strong>Status Pembayaran:</strong>
      <span class="px-2 py-1 rounded text-white text-xs
        {{ $payment->status==='2' ? 'bg-green-600' : ($payment->status==='1' ? 'bg-yellow-600' : ($payment->status==='3' ? 'bg-red-600' : 'bg-gray-500')) }}">
        {{ ['0'=>'Belum dibayar','1'=>'Proses','2'=>'Selesai','3'=>'Dibatalkan'][$payment->status] ?? $payment->status }}
      </span>
    </div>
    <div><strong>Metode Pembayaran:</strong>
      @if($payment->selected_method === 'bayar-ditempat')
        <span class="font-medium">Bayar di Tempat</span>
      @elseif($payment->selected_method === 'transfer-bank')
        <span class="font-medium">Transfer Bank</span>
        @if($payment->payment_account_number)
          <span class="text-sm text-gray-600">(Rek: {{ $payment->payment_account_number }})</span>
        @endif
      @elseif($payment->selected_method === 'e-wallet')
        <span class="font-medium">E-Wallet</span>
        @if($payment->payment_account_number)
          <span class="text-sm text-gray-600">({{ $payment->payment_account_number }})</span>
        @endif
      @else
        <span class="text-gray-500">Belum dipilih</span>
      @endif
    </div>
  </div>

  <div class="mt-6 flex gap-2">
    <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 border rounded">Kembali</a>
    <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 border rounded">Lihat Semua Pembayaran</a>
  </div>
</div>

@endsection
