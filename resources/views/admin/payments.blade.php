@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
	<h1 class="text-2xl font-extrabold text-gray-800">Verifikasi Pembayaran</h1>
	<a href="{{ route('admin.schedules.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Kembali ke Jadwal</a>
</div>

@if(session('success'))
	<div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="grid gap-6">
	@forelse($payments as $p)
	<div class="bg-white rounded-xl shadow p-6">
		<!-- Header -->
		<div class="flex items-start justify-between mb-4 pb-4 border-b">
			<div class="flex-1">
				<h2 class="text-lg font-bold text-gray-800">{{ $p->booking->event_name ?? 'Acara' }}</h2>
				<p class="text-sm text-gray-600">Pemesan: <span class="font-semibold">{{ $p->booking->user->name ?? '-' }}</span></p>
				<p class="text-sm text-gray-600">Gedung: <span class="font-semibold">{{ $p->booking->gedung->nama ?? '-' }}</span></p>
			</div>
			<div class="text-right">
				@php 
					$statusLabel = ['0'=>'Pending','1'=>'Proses','2'=>'Verified','3'=>'Cancelled'][$p->status] ?? $p->status;
					$statusColor = $p->status === '2' ? 'bg-green-600' : ($p->status === '1' ? 'bg-yellow-600' : ($p->status === '3' ? 'bg-red-600' : 'bg-gray-600'));
				@endphp
				<span class="px-3 py-1 rounded text-white font-semibold {{ $statusColor }}">
					{{ $statusLabel }}
				</span>
			</div>
		</div>

		<!-- Invoice Details -->
		<div class="grid md:grid-cols-3 gap-4 mb-4">
			<!-- Column 1: Event Info -->
			<div>
				<div class="text-xs text-gray-500 uppercase font-semibold mb-2">Informasi Acara</div>
				<div class="space-y-2 text-sm">
					@php
						$start = \Carbon\Carbon::parse($p->booking->date);
						$end = $p->booking->end_date ? \Carbon\Carbon::parse($p->booking->end_date) : null;
					@endphp
					<div><strong>Tanggal:</strong>
						@if($end && $end->ne($start))
							Dari {{ $start->format('d M Y') }} sampai {{ $end->format('d M Y') }}
						@else
							{{ $start->format('d M Y') }}
						@endif
					</div>
					<div><strong>Jam:</strong> {{ $p->booking->start_time }} - {{ $p->booking->end_time }}</div>
					<div><strong>Kapasitas:</strong> {{ $p->booking->capacity }} orang</div>
					<div><strong>Jenis:</strong> {{ $p->booking->event_type }}</div>
				</div>
			</div>

			<!-- Column 2: Kontak & File -->
			<div>
				<div class="text-xs text-gray-500 uppercase font-semibold mb-2">Kontak & File</div>
				<div class="space-y-2 text-sm">
					<div><strong>No. Telp:</strong> {{ $p->booking->phone ?? $p->booking->user->phone ?? '-' }}</div>
					<div><strong>Email:</strong> {{ $p->booking->user->email ?? '-' }}</div>
					<div>
						<strong>Proposal:</strong>
						@if($p->booking->proposal_file)
							<a href="{{ asset('storage/'.$p->booking->proposal_file) }}" target="_blank" class="text-blue-600 hover:underline ml-2">📄 Lihat</a>
						@else
							<span class="text-gray-500 ml-2">-</span>
						@endif
					</div>
					<div>
						<strong>Fasilitas:</strong>
						@if($p->booking->bookingFasilitas && $p->booking->bookingFasilitas->count() > 0)
							<div class="text-xs mt-1">
								@foreach($p->booking->bookingFasilitas as $bf)
									<span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-1 mb-1">
										{{ $bf->fasilitas->nama }} ({{ $bf->jumlah }}x)
									</span>
								@endforeach
							</div>
						@else
							<span class="text-gray-500 ml-2">-</span>
						@endif
					</div>
				</div>
			</div>

			<!-- Column 3: Pembayaran -->
			<div>
				<div class="text-xs text-gray-500 uppercase font-semibold mb-2">Pembayaran</div>
				<div class="bg-gray-50 p-3 rounded space-y-2">
					<div>
						<div class="text-xs text-gray-600">Jumlah</div>
						<div class="text-2xl font-bold text-blue-900">Rp {{ number_format($p->amount,0,',','.') }}</div>
					</div>
					<div>
						<div class="text-xs text-gray-600">Metode</div>
						<div class="text-sm font-semibold">
							@if($p->selected_method === 'bayar-ditempat')
								💰 Bayar di Tempat
							@elseif($p->selected_method === 'transfer-bank')
								🏦 Transfer Bank
								@if($p->payment_account_number)
									<div class="text-xs text-gray-600 mt-1">Rek: {{ $p->payment_account_number }}</div>
								@endif
							@elseif($p->selected_method === 'e-wallet')
								📱 E-Wallet
								@if($p->payment_account_number)
									<div class="text-xs text-gray-600 mt-1">{{ $p->payment_account_number }}</div>
								@endif
							@else
								-
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Bukti Pembayaran -->
		@if($p->proof_file)
		<div class="mb-4 pb-4 border-b">
			<div class="text-sm font-semibold text-gray-800 mb-2">Bukti Pembayaran</div>
			@php $ext = strtolower(pathinfo(storage_path('app/public/'.$p->proof_file), PATHINFO_EXTENSION) ?? ''); @endphp
			@if(in_array($ext, ['jpg','jpeg','png']))
				<a href="{{ asset('storage/'.$p->proof_file) }}" target="_blank" class="inline-block border rounded overflow-hidden">
					<img src="{{ asset('storage/'.$p->proof_file) }}" alt="Bukti" class="h-32 object-contain" />
				</a>
			@else
				<a href="{{ asset('storage/'.$p->proof_file) }}" target="_blank" class="text-blue-600 hover:underline">📄 Lihat File ({{ $ext }})</a>
			@endif
		</div>
		@endif

		<!-- Tombol Aksi Dinamis -->
		<div class="flex flex-wrap gap-2">
			@if($p->status === '0')
				<!-- Belum ada bukti -->
				<button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded font-medium cursor-not-allowed">
					⏳ Menunggu Bukti Pembayaran
				</button>
			@elseif($p->status === '1')
				<!-- Proses/Menunggu Verifikasi -->
				<form method="POST" action="{{ route('admin.payments.status', $p->id) }}" class="inline" onsubmit="return confirm('Verifikasi pembayaran ini?')">
					@csrf @method('PUT')
					<input type="hidden" name="status" value="2">
					<button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-medium">
						✓ Verifikasi Pembayaran
					</button>
				</form>
				<form method="POST" action="{{ route('admin.payments.status', $p->id) }}" class="inline" onsubmit="return confirm('Tolak pembayaran ini?')">
					@csrf @method('PUT')
					<input type="hidden" name="status" value="3">
					<button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-medium">
						✕ Tolak Pembayaran
					</button>
				</form>
			@elseif($p->status === '2')
				<!-- Verified -->
				<div class="inline-block">
					<span class="px-4 py-2 bg-green-100 text-green-800 rounded font-medium">
						✓ Pembayaran Terverifikasi
					</span>
				</div>
				<form method="POST" action="{{ route('admin.payments.status', $p->id) }}" class="inline" onsubmit="return confirm('Batalkan verifikasi? Jadwal juga akan di-tolak.')">
					@csrf @method('PUT')
					<input type="hidden" name="status" value="3">
					<button class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded font-medium">
						↻ Batalkan Verifikasi
					</button>
				</form>
			@elseif($p->status === '3')
				<!-- Cancelled -->
				<div class="inline-block">
					<span class="px-4 py-2 bg-red-100 text-red-800 rounded font-medium">
						✕ Pembayaran Dibatalkan
					</span>
				</div>
				<form method="POST" action="{{ route('admin.payments.status', $p->id) }}" class="inline" onsubmit="return confirm('Kembalikan ke proses? Jadwal akan di-pending.')">
					@csrf @method('PUT')
					<input type="hidden" name="status" value="1">
					<button class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded font-medium">
						↺ Kembalikan ke Proses
					</button>
				</form>
			@endif
		</div>

		<!-- Info Status Booking -->
		<div class="mt-4 pt-4 border-t text-xs">
			<strong class="text-gray-700">Status Jadwal Booking:</strong>
			@php $bookingStatus = ['1'=>'Pending', '2'=>'Approved', '3'=>'Rejected', '4'=>'Completed'][$p->booking->status] ?? $p->booking->status; @endphp
			<span class="ml-2 px-2 py-1 rounded text-white
				{{ $p->booking->status === '2' ? 'bg-green-600' : ($p->booking->status === '1' ? 'bg-yellow-600' : ($p->booking->status === '3' ? 'bg-red-600' : 'bg-blue-600')) }}">
				{{ $bookingStatus }}
			</span>
		</div>
	</div>
	@empty
	<div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
		Tidak ada pembayaran.
	</div>
	@endforelse
</div>

@endsection
