@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
	<h1 class="text-2xl font-extrabold text-gray-800">Gedung</h1>
	<a href="{{ route('gedung.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">+ Tambah Gedung</a>
</div>

@if(session('success'))
	<div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

@if(session('error'))
	<div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
@endif

<div class="bg-white rounded-xl shadow overflow-x-auto">
	<table class="min-w-full text-sm">
		<thead class="bg-gray-100 text-left">
			<tr>
				<th class="p-3">No</th>
				<th class="p-3">Nama Gedung</th>
				<th class="p-3">Lokasi</th>
				<th class="p-3">Kapasitas</th>
				<th class="p-3">Harga</th>
				<th class="p-3">Aksi</th>
			</tr>
		</thead>
		<tbody>
			@forelse($items as $i => $item)
			<tr class="border-t hover:bg-gray-50">
				<td class="p-3">{{ $i+1 }}</td>
				<td class="p-3">
					<div class="font-semibold text-gray-900">{{ $item->nama }}</div>
					@if($item->deskripsi)
						<div class="text-xs text-gray-600 mt-1">{{ Str::limit($item->deskripsi, 100) }}</div>
					@endif
				</td>
				<td class="p-3">{{ $item->lokasi ?? '-' }}</td>
				<td class="p-3">{{ $item->kapasitas ?? '-' }} orang</td>
				<td class="p-3">
					@if($item->harga)
						Rp {{ number_format($item->harga, 0, ',', '.') }}
					@else
						<span class="text-gray-500">-</span>
					@endif
				</td>
				<td class="p-3">
					<div class="flex flex-wrap gap-2">
						<a href="{{ route('gedung.edit', $item->id) }}" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs">Edit</a>
						<form action="{{ route('gedung.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus gedung ini?')">
							@csrf @method('DELETE')
							<button class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">Hapus</button>
						</form>
					</div>
				</td>
			</tr>
			@empty
			<tr>
				<td colspan="6" class="p-6 text-center text-gray-500">Belum ada gedung.</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>
@endsection
