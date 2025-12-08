@extends('admin.layout')

@section('content')
<h1 class="text-2xl font-extrabold text-gray-800 mb-4">Edit Gedung</h1>

@if($errors->any())
	<div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
		<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif

<div class="bg-white rounded-xl shadow p-6">
	<form method="POST" action="{{ route('gedung.update', $item->id) }}">
		@csrf
		@method('PUT')
		<div class="space-y-4">
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Nama Gedung</label>
				<input type="text" name="nama" value="{{ old('nama', $item->nama) }}" required minlength="3" maxlength="255"
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
				@error('nama')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
				<input type="text" name="lokasi" value="{{ old('lokasi', $item->lokasi) }}" maxlength="255"
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Kampus A, Gedung B">
				@error('lokasi')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
			</div>
			<div class="grid grid-cols-2 gap-4">
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas (orang)</label>
					<input type="number" name="kapasitas" value="{{ old('kapasitas', $item->kapasitas) }}" min="1" max="10000"
						class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
					@error('kapasitas')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
					<input type="number" name="harga" value="{{ old('harga', $item->harga) }}" min="0" step="1000"
						class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Harga per hari">
					@error('harga')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
				</div>
			</div>
			<div>
				<label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
				<textarea name="deskripsi" rows="4" maxlength="1000"
					class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Deskripsi gedung...">{{ old('deskripsi', $item->deskripsi) }}</textarea>
				@error('deskripsi')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
			</div>
			<div class="flex gap-2 pt-4">
				<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan</button>
				<a href="{{ route('gedung.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Batal</a>
			</div>
		</div>
	</form>
</div>
@endsection
