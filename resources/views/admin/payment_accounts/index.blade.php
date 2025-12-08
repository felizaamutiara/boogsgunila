@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-extrabold text-gray-800">Kelola Rekening Pembayaran</h1>
    <a href="{{ route('admin.payment_accounts.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">+ Tambah Rekening</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr class="text-left">
                <th class="p-3 w-12 text-center">No</th>
                <th class="p-3 w-24">Tipe</th>
                <th class="p-3">Nama Rekening</th>
                <th class="p-3 w-32">Nomor Rekening</th>
                <th class="p-3 w-32">Atas Nama</th>
                <th class="p-3 w-20 text-center">Status</th>
                <th class="p-3 w-40 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($accounts as $i => $acc)
            <tr class="border-b hover:bg-gray-50">
                <td class="py-4 px-3 text-center font-semibold text-gray-700">
                    {{ $i + 1 }}
                </td>

                <td class="py-4 px-3 text-gray-700">
                    @if($acc->type === 'bayar-ditempat')
                        <span class="px-2 py-1 rounded text-white text-xs bg-gray-600">Tunai</span>
                    @elseif($acc->type === 'transfer-bank')
                        <span class="px-2 py-1 rounded text-white text-xs bg-blue-600">Bank</span>
                    @elseif($acc->type === 'e-wallet')
                        <span class="px-2 py-1 rounded text-white text-xs bg-purple-600">E-Wallet</span>
                    @endif
                </td>

                <td class="py-4 px-3 text-gray-700">
                    <div class="font-semibold">{{ $acc->name }}</div>
                    <div class="text-xs text-gray-500">{{ $acc->description ?? '-' }}</div>
                </td>

                <td class="py-4 px-3 text-gray-700 font-mono text-xs">
                    {{ $acc->account_number }}
                </td>

                <td class="py-4 px-3 text-gray-700 text-sm">
                    {{ $acc->account_name }}
                </td>

                <td class="py-4 px-3 text-center">
                    <span class="px-3 py-1 rounded text-white text-xs {{ $acc->is_active ? 'bg-green-600' : 'bg-red-600' }}">
                        {{ $acc->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>

                <td class="py-4 px-3">
                    <div class="flex justify-center gap-2">
                        <a href="{{ route('admin.payment_accounts.edit', $acc->id) }}"
                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs">
                            Edit
                        </a>
                        <form action="{{ route('admin.payment_accounts.destroy', $acc->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus rekening ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
                                Hapus
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="p-6 text-center text-gray-500">Tidak ada rekening pembayaran.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@endsection
