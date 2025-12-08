@extends('layouts.app')

@section('content')
<section class="bg-white py-10">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h1 class="text-3xl md:text-4xl font-extrabold text-blue-900">Riwayat Pembayaran</h1>
		<p class="text-gray-600">Kelola dan lihat status pembayaran booking Anda</p>

		@if(session('success'))
			<div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 mt-6">{{ session('success') }}</div>
		@endif

		<!-- Tabs -->
		@php
			$tabs = [
				'' => ['label' => 'Semua', 'icon' => 'list'],
				'0' => ['label' => 'Belum Dibayar', 'icon' => 'clock', 'color' => 'text-gray-500'],
				'1' => ['label' => 'Proses', 'icon' => 'hourglass', 'color' => 'text-yellow-500'],
				'2' => ['label' => 'Terverifikasi', 'icon' => 'check-circle', 'color' => 'text-green-600'],
				'3' => ['label' => 'Dibatalkan', 'icon' => 'xmark-circle', 'color' => 'text-red-600'],
			];
		@endphp
		<div class="flex items-center gap-4 mt-6 overflow-x-auto pb-2">
			@foreach($tabs as $key => $tab)
				@php $isActive = (string)$active === (string)$key; @endphp
				<a href="{{ route('payments.index', ['status' => $key !== '' ? $key : null]) }}"
				   class="px-4 py-2 rounded-lg text-sm font-medium transition whitespace-nowrap
				   {{ $isActive ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
					{{ $tab['label'] }}
				</a>
			@endforeach
		</div>

		<!-- Grid -->
		<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
			@forelse($payments as $p)
				@php
					$booking = $p->booking;
					$gedungName = $booking->gedung->nama ?? 'Sewa Gedung';
					$img = asset('img/gsgjauh.jpg');
					$statusLabel = ['0'=>'Belum Dibayar','1'=>'Proses','2'=>'Terverifikasi','3'=>'Dibatalkan'][$p->status] ?? 'Unknown';
					$statusColor = $p->status === '2' ? 'bg-green-600' : ($p->status === '0' ? 'bg-gray-600' : ($p->status === '1' ? 'bg-yellow-600' : 'bg-red-600'));
				@endphp
				<div class="bg-white border rounded-xl overflow-hidden hover:shadow-lg transition">
					<img src="{{ $img }}" alt="gedung" class="w-full h-40 object-cover">
					<div class="p-4">
						<h3 class="font-bold text-blue-900 text-lg">{{ $gedungName }}</h3>
						<p class="text-sm text-gray-600 mb-2">{{ $booking->event_name ?? 'Acara' }}</p>
						@php
							$start = \Carbon\Carbon::parse($booking->date);
							$end = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date) : null;
						@endphp
						<p class="text-xs text-gray-500 mb-3">📅 
							@if($end && $end->ne($start))
								Dari {{ $start->format('d M Y') }} sampai {{ $end->format('d M Y') }}
							@else
								{{ $start->format('d M Y') }}
							@endif
						</p>
						
						<div class="mb-3 pb-3 border-b">
							<p class="text-sm text-gray-600 mb-1">Total Pembayaran</p>
							<p class="text-lg font-bold text-blue-600">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
						</div>

						<div class="mb-3">
							<span class="px-3 py-1 rounded-full text-white text-xs font-semibold {{ $statusColor }}">
								{{ $statusLabel }}
							</span>
						</div>

						<!-- Action Buttons -->
						<div class="flex flex-col gap-2">
							@if($p->status === '0')
								<a href="{{ route('payments.upload.form', $p->id) }}" class="text-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm font-medium transition">
									💳 Upload Bukti
								</a>
							@elseif($p->status === '1')
								<div class="bg-yellow-50 border border-yellow-200 rounded p-2 text-center">
									<p class="text-xs text-yellow-800 font-medium">⏳ Menunggu Verifikasi</p>
								</div>
							@elseif($p->status === '2')
								<div class="bg-green-50 border border-green-200 rounded p-2 text-center">
									<p class="text-xs text-green-800 font-medium">✓ Terverifikasi</p>
								</div>
							@else
								<div class="bg-red-50 border border-red-200 rounded p-2 text-center">
									<p class="text-xs text-red-800 font-medium">Pembayaran Dibatalkan</p>
								</div>
							@endif
							
							<a href="{{ route('booking.invoice', $booking->id) }}" class="text-center border border-blue-600 text-blue-600 hover:bg-blue-50 px-3 py-2 rounded text-sm font-medium transition">
								📄 Lihat Invoice
							</a>
						</div>
					</div>
				</div>
			@empty
				<div class="col-span-full">
					<div class="bg-white rounded-lg shadow p-8 text-center">
						<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
						</svg>
						<p class="text-gray-600 mb-4">Belum ada data pembayaran.</p>
						<a href="{{ route('booking.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
							🎯 Buat Booking Baru
						</a>
					</div>
				</div>
			@endforelse
		</div>
	</div>
</section>
@endsection


