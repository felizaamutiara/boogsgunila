@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-extrabold">Data Fasilitas</h1>
    <a href="{{ route('fasilitas.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">+ Tambah Fasilitas</a>
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
                <th class="p-3">Gambar</th>
                <th class="p-3">Nama</th>
                <th class="p-3">Harga</th>
                <th class="p-3">Stok</th>
                <th class="p-3">Deskripsi</th>
                <th class="p-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $i => $it)
            <tr class="border-t">
                <td class="p-3">{{ $i+1 }}</td>
                <td class="p-3">
                    @if($it->image)
                        <img src="{{ asset('storage/'.$it->image) }}" alt="{{ $it->nama }}" class="w-16 h-12 object-cover rounded">
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>
                <td class="p-3 font-semibold">{{ $it->nama }}</td>
                <td class="p-3">Rp {{ number_format($it->harga ?? 0) }}</td>
                <td class="p-3">{{ $it->stok }}</td>
                <td class="p-3 text-sm text-gray-600">{{ $it->deskripsi }}</td>
                <td class="p-3">
                    <div class="flex gap-2">
                        <a href="{{ route('fasilitas.edit', $it->id) }}" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs">Edit</a>
                        <form action="{{ route('fasilitas.destroy', $it->id) }}" method="POST" onsubmit="return confirm('Hapus fasilitas ini?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="p-6 text-center text-gray-500">Belum ada fasilitas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
