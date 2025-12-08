@extends('admin.layout')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Edit Fasilitas</h1>
    <p class="text-sm text-gray-600">Perbarui data fasilitas</p>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form action="{{ route('fasilitas.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium">Nama</label>
            <input type="text" name="nama" value="{{ $item->nama }}" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Harga (Rp)</label>
                <input type="number" name="harga" min="0" value="{{ $item->harga }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Stok</label>
                <input type="number" name="stok" min="0" value="{{ $item->stok }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Deskripsi (opsional)</label>
            <textarea name="deskripsi" class="mt-1 w-full border rounded px-3 py-2" rows="4">{{ $item->deskripsi }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Gambar Saat Ini</label>
            @if($item->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->nama }}" class="max-w-xs rounded">
                </div>
            @else
                <div class="text-gray-500">Tidak ada gambar</div>
            @endif
        </div>
        <div class="mt-2">
            <label class="block text-sm font-medium">Unggah Gambar Baru (opsional)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 w-full">
        </div>

        <div class="flex gap-2 pt-3">
            <a href="{{ route('fasilitas.index') }}" class="px-4 py-2 border rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
