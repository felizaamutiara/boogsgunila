@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
	<h1 class="text-2xl font-extrabold text-gray-800">Detail Sewa</h1>
	<a href="{{ route('admin.schedules.index') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Lihat Semua Jadwal</a>
</div>

@if(session('success'))
	<div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="space-y-4">
	@forelse($items as $b)
		<div class="bg-white rounded-xl shadow p-5">
			<div class="grid md:grid-cols-4 gap-4">
				<div class="md:col-span-3">
					<p class="text-sm text-gray-500 mb-3">Detail Sewa</p>
					<div class="grid sm:grid-cols-2 gap-y-2 text-sm">
						<div class="font-semibold text-blue-900">Kontak</div>
						<div>{{ $b->phone ?? ($b->user->phone ?? '-') }}</div>

						<div class="font-semibold text-blue-900">Pemesan</div>
						<div>{{ $b->user->name ?? '-' }} ({{ $b->user->email ?? '-' }})</div>
						
						<div class="font-semibold text-blue-900">Gedung</div>
						<div>
							<span class="font-medium">{{ $b->gedung->nama ?? '-' }}</span>
							@if($b->gedung)
								<div class="text-xs text-gray-500 mt-1">
									Kapasitas: {{ $b->gedung->kapasitas ?? '-' }} orang
									@if($b->gedung->lokasi)
										• {{ $b->gedung->lokasi }}
									@endif
								</div>
							@endif
						</div>
						
						<div class="font-semibold text-blue-900">Acara</div>
						<div>{{ $b->event_name }} ({{ $b->event_type }})</div>
						
						<div class="font-semibold text-blue-900">Kapasitas Acara</div>
						<div>{{ $b->capacity }} orang</div>
						
						<div class="font-semibold text-blue-900">Tanggal</div>
						<div>{{ \Carbon\Carbon::parse($b->date)->format('d F Y') }}@if($b->end_date && $b->end_date !== $b->date) - {{ \Carbon\Carbon::parse($b->end_date)->format('d F Y') }}@endif</div>
						
						<div class="font-semibold text-blue-900">Waktu</div>
						<div>{{ $b->start_time }} - {{ $b->end_time }}</div>

						<div class="font-semibold text-blue-900">Proposal</div>
						<div>
							@if($b->proposal_file)
								<a href="{{ asset('storage/'.$b->proposal_file) }}" target="_blank" class="text-blue-700 underline">Lihat Proposal</a>
							@else
								<span class="text-gray-500">-</span>
							@endif
						</div>

						<div class="font-semibold text-blue-900">Bukti Pembayaran</div>
						<div>
							@php $pay = \App\Models\Payment::where('booking_id', $b->id)->first(); @endphp
							@if($pay && $pay->proof_file)
								<a href="{{ asset('storage/'.$pay->proof_file) }}" target="_blank" class="text-blue-700 underline">Lihat Bukti Pembayaran</a>
							@else
								<span class="text-gray-500">-</span>
							@endif
						</div>
						
						@if($b->bookingFasilitas && $b->bookingFasilitas->count() > 0)
						<div class="font-semibold text-blue-900">Fasilitas Disewa</div>
						<div>
							<ul class="list-disc list-inside text-xs space-y-1">
								@foreach($b->bookingFasilitas as $bf)
									<li>
										<span class="font-medium">{{ $bf->fasilitas->nama ?? '-' }}</span>
										({{ $bf->jumlah }}x) 
										@if($bf->fasilitas)
											- Rp {{ number_format($bf->fasilitas->harga * $bf->jumlah, 0, ',', '.') }}
										@endif
									</li>
								@endforeach
							</ul>
						</div>
						@else
						<div class="font-semibold text-blue-900">Fasilitas</div>
						<div class="text-gray-500 text-xs">Tidak ada fasilitas tambahan</div>
						@endif
						
						<div class="font-semibold text-blue-900">Status</div>
						<div>
							<span class="px-2 py-1 rounded text-white text-xs
								{{ $b->status==='2' ? 'bg-green-600' : ($b->status==='1' ? 'bg-yellow-600' : ($b->status==='3' ? 'bg-red-600' : 'bg-blue-600')) }}">
								{{ ['1'=>'Menunggu','2'=>'Disetujui','3'=>'Ditolak','4'=>'Selesai'][$b->status] ?? $b->status }}
							</span>
						</div>
					</div>
				</div>
				<div class="flex flex-col gap-2">
					<a href="{{ route('admin.booking.edit', $b->id) }}" class="w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white rounded py-2 text-sm">Edit</a>
					<a href="{{ route('admin.booking.invoice', $b->id) }}" class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white rounded py-2 text-sm">Lihat Invoice</a>
					@if($b->status === '1')
					<form action="{{ route('admin.booking.approve', $b->id) }}" method="POST" class="w-full" onsubmit="return confirm('Yakin ingin MENYETUJUI booking ini?')">
						@csrf @method('PUT')
						<button class="w-full bg-green-600 hover:bg-green-700 text-white rounded py-2 text-sm">✓ Setujui</button>
					</form>
					<form action="{{ route('admin.booking.reject', $b->id) }}" method="POST" class="w-full" onsubmit="return confirm('Yakin ingin MENOLAK booking ini?')">
						@csrf @method('PUT')
						<button class="w-full bg-red-600 hover:bg-red-700 text-white rounded py-2 text-sm">✕ Tolak</button>
					</form>
					@elseif($b->status === '2')
					<form action="{{ route('admin.booking.reject', $b->id) }}" method="POST" class="w-full" onsubmit="return confirm('Yakin ingin MEMBATALKAN (tolak) booking yang sudah disetujui ini?')">
						@csrf @method('PUT')
						<button class="w-full bg-orange-600 hover:bg-orange-700 text-white rounded py-2 text-sm">↻ Batalkan (Tolak)</button>
					</form>
					@endif
				</div>
			</div>
		</div>
	@empty
		<div class="bg-white rounded-xl shadow p-8 text-center">
			<p class="text-gray-600">Belum ada sewa aktif.</p>
		</div>
	@endforelse
</div>
@endsection


