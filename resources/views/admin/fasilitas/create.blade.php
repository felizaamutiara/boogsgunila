@extends('admin.layout')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Tambah Fasilitas</h1>
    <p class="text-sm text-gray-600">Tambah fasilitas yang bisa disewa</p>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form action="{{ route('fasilitas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Nama</label>
            <input type="text" name="nama" class="mt-1 w-full border rounded px-3 py-2" required>
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Harga (Rp)</label>
                <input type="number" name="harga" min="0" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Stok</label>
                <input type="number" name="stok" min="0" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium">Deskripsi (opsional)</label>
            <textarea name="deskripsi" class="mt-1 w-full border rounded px-3 py-2" rows="4"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium">Gambar (opsional)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 w-full">
        </div>

        <div class="flex gap-2 pt-3">
            <a href="{{ route('fasilitas.index') }}" class="px-4 py-2 border rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
        </div>
    </form>
</div>
@endsection
