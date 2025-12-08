@extends('admin.layout')

@section('content')
<div class="space-y-6">
	<h1 class="text-2xl font-extrabold text-gray-800">Dashboard</h1>

	@if(session('success'))
		<div class="bg-green-100 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
	@endif

	<!-- Stats Cards -->
	<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
		<div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
			<div class="flex items-center justify-between">
				<div>
					<div class="text-xs text-gray-500 uppercase font-semibold">Pengguna</div>
					<div class="text-3xl font-extrabold text-blue-900 mt-1">{{ $stats['users'] ?? 0 }}</div>
				</div>
				<div class="text-3xl text-blue-100"><i class="fa-solid fa-users"></i></div>
			</div>
		</div>

		<div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
			<div class="flex items-center justify-between">
				<div>
					<div class="text-xs text-gray-500 uppercase font-semibold">Total Jadwal</div>
					<div class="text-3xl font-extrabold text-green-900 mt-1">{{ $stats['bookings'] ?? 0 }}</div>
				</div>
				<div class="text-3xl text-green-100"><i class="fa-solid fa-calendar-check"></i></div>
			</div>
		</div>

		<div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
			<div class="flex items-center justify-between">
				<div>
					<div class="text-xs text-gray-500 uppercase font-semibold">Pembayaran Pending</div>
					<div class="text-3xl font-extrabold text-yellow-900 mt-1">{{ $stats['payments_pending'] ?? 0 }}</div>
				</div>
				<div class="text-3xl text-yellow-100"><i class="fa-solid fa-hourglass-half"></i></div>
			</div>
		</div>

		<div class="bg-white rounded-xl shadow p-5 hover:shadow-lg transition">
			<div class="flex items-center justify-between">
				<div>
					<div class="text-xs text-gray-500 uppercase font-semibold">Sewa Aktif</div>
					<div class="text-3xl font-extrabold text-purple-900 mt-1">{{ $stats['active_rentals'] ?? 0 }}</div>
				</div>
				<div class="text-3xl text-purple-100"><i class="fa-solid fa-check-circle"></i></div>
			</div>
		</div>
	</div>

	<!-- Recent Bookings -->
	<div class="grid lg:grid-cols-2 gap-6">
		<!-- Ringkasan Booking Terbaru -->
		<div class="bg-white rounded-xl shadow p-6">
			<div class="flex items-center justify-between mb-4">
				<h2 class="text-lg font-semibold text-gray-800">Booking Terbaru</h2>
				<a href="{{ route('admin.schedules.index') }}" class="text-xs text-blue-600 hover:text-blue-800">Lihat Semua →</a>
			</div>
			@if($recentBookings->count() > 0)
				<div class="space-y-3">
					@foreach($recentBookings as $booking)
					<div class="flex items-center justify-between pb-3 border-b last:border-b-0">
						<div class="flex-1">
							<p class="font-semibold text-gray-800 text-sm">{{ $booking->event_name }}</p>
							<p class="text-xs text-gray-500">
								<i class="fa-solid fa-user"></i> {{ $booking->user->name ?? 'N/A' }}
							</p>
							<p class="text-xs text-gray-500">
								<i class="fa-solid fa-building"></i> {{ $booking->gedung->nama ?? 'N/A' }}
								• {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}
							</p>
						</div>
						<span class="px-2 py-1 rounded text-white text-xs font-semibold
							{{ $booking->status === '2' ? 'bg-green-600' : ($booking->status === '1' ? 'bg-yellow-600' : ($booking->status === '3' ? 'bg-red-600' : 'bg-blue-600')) }}">
							{{ ['1'=>'Pending','2'=>'Approved','3'=>'Rejected','4'=>'Done'][$booking->status] ?? $booking->status }}
						</span>
					</div>
					@endforeach
				</div>
			@else
				<p class="text-sm text-gray-500">Belum ada booking.</p>
			@endif
		</div>

		<!-- Summary Stats -->
		<div class="bg-white rounded-xl shadow p-6">
			<h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Cepat</h2>
			<div class="space-y-3">
				<div class="flex items-center justify-between pb-3 border-b">
					<span class="text-gray-600">Gedung</span>
					<span class="font-semibold text-lg text-gray-800">{{ $stats['gedung'] ?? 0 }}</span>
				</div>
				<div class="flex items-center justify-between pb-3 border-b">
					<span class="text-gray-600">Fasilitas</span>
					<span class="font-semibold text-lg text-gray-800">{{ $stats['fasilitas'] ?? 0 }}</span>
				</div>
				<div class="flex items-center justify-between pb-3 border-b">
					<span class="text-gray-600">Menunggu Approval</span>
					<span class="font-semibold text-lg text-yellow-600">{{ $stats['pending_approval'] ?? 0 }}</span>
				</div>
				<div class="flex items-center justify-between pb-3">
					<span class="text-gray-600">Booking Ditolak</span>
					<span class="font-semibold text-lg text-red-600">{{ $stats['rejected_bookings'] ?? 0 }}</span>
				</div>
			</div>
			<div class="mt-4 pt-4 border-t">
				<a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Kelola Pengguna →</a>
			</div>
		</div>
	</div>

	<!-- Fasilitas Table -->
	<div class="bg-white rounded-xl shadow p-6">
		<div class="flex items-center justify-between mb-4">
			<h2 class="text-lg font-semibold text-gray-800">Daftar Fasilitas</h2>
			<a href="{{ route('fasilitas.create') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">+ Tambah</a>
		</div>
		@if($fasilitas->count() > 0)
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-100">
						<tr>
							<th class="px-4 py-3 text-left font-semibold text-gray-700">Nama</th>
							<th class="px-4 py-3 text-left font-semibold text-gray-700">Harga</th>
							<th class="px-4 py-3 text-left font-semibold text-gray-700">Stok</th>
							<th class="px-4 py-3 text-left font-semibold text-gray-700">Deskripsi</th>
							<th class="px-4 py-3 text-center font-semibold text-gray-700">Aksi</th>
						</tr>
					</thead>
					<tbody>
						@foreach($fasilitas as $f)
						<tr class="border-t hover:bg-gray-50">
							<td class="px-4 py-3 font-semibold text-gray-800">{{ $f->nama }}</td>
							<td class="px-4 py-3">
								<span class="font-semibold text-green-600">Rp {{ number_format($f->harga ?? 0, 0, ',', '.') }}</span>
							</td>
							<td class="px-4 py-3">
								<span class="px-2 py-1 rounded text-sm font-semibold
									{{ $f->stok > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
									{{ $f->stok ?? 0 }}
								</span>
							</td>
							<td class="px-4 py-3 text-gray-600 text-xs">{{ Str::limit($f->deskripsi, 50) ?? '-' }}</td>
							<td class="px-4 py-3 text-center">
								<a href="{{ route('fasilitas.edit', $f->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">Edit</a>
								|
								<form action="{{ route('fasilitas.destroy', $f->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus fasilitas ini?')">
									@csrf @method('DELETE')
									<button type="submit" class="text-red-600 hover:text-red-800 text-xs">Hapus</button>
								</form>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@else
			<p class="text-sm text-gray-500">Belum ada fasilitas.</p>
		@endif
	</div>
</div>
@endsection


