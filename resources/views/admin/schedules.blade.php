@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-extrabold text-gray-800">Data Jadwal</h1>
    <a href="{{ route('admin.booking.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded">+ Tambah Jadwal</a>
</div>

@if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-xl shadow overflow-x-auto">
    <table class="min-w-full text-sm table-fixed border-collapse">
        <thead class="bg-gray-100">
            <tr class="text-left">
                <th class="p-3 w-12 text-center">No</th>
                <th class="p-3 w-40">Tanggal</th>
                <th class="p-3">Acara</th>
                <th class="p-3 w-32 text-center">Status</th>
                <th class="p-3 w-64 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($items as $i => $item)
            <tr class="border-b hover:bg-gray-50">

                {{-- NO --}}
                <td class="py-4 px-3 text-center font-semibold text-gray-700">
                    {{ $i + 1 }}
                </td>

                {{-- TANGGAL --}}
                <td class="py-4 px-3 text-gray-700">
                    @php
                        $startDate = \Carbon\Carbon::parse($item->date);
                        $endDate = $item->end_date ? \Carbon\Carbon::parse($item->end_date) : null;
                    @endphp
                    <div class="font-semibold">
                        @if($endDate && $endDate->ne($startDate))
                            Dari {{ $startDate->format('d M Y') }} sampai {{ $endDate->format('d M Y') }}
                        @else
                            {{ $startDate->format('d M Y') }}
                        @endif
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} -
                        {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                    </div>
                </td>

                {{-- ACARA DETAIL --}}
                <td class="py-4 px-3 align-top leading-relaxed">
                    <div class="font-semibold text-blue-900">{{ $item->event_name }}</div>

                    <div class="text-xs text-gray-600 mt-1">Gedung: {{ $item->gedung->nama ?? '-' }}</div>
                    <div class="text-xs text-gray-500">Pemesan: {{ $item->user->name ?? '-' }}</div>
                    <div class="text-xs text-gray-500">Kontak: {{ $item->phone ?? ($item->user->phone ?? '-') }}</div>

                    <div class="text-xs text-gray-500 mt-1">
                        Proposal:
                        @if($item->proposal_file)
                            <a href="{{ asset('storage/'.$item->proposal_file) }}" target="_blank" class="text-blue-700 underline">Lihat</a>
                        @else - @endif
                    </div>

                    <div class="text-xs text-gray-500 mt-1">
                        Bukti Pembayaran:
                        @php
                            $pay = \App\Models\Payment::where('booking_id', $item->id)->first();
                        @endphp
                        @if($pay && $pay->proof_file)
                            <a href="{{ asset('storage/'.$pay->proof_file) }}" target="_blank" class="text-blue-700 underline">Lihat</a>
                        @else - @endif
                    </div>

                    @if($item->bookingFasilitas && $item->bookingFasilitas->count())
                        <div class="text-xs text-gray-500 mt-1">
                            Fasilitas:
                            {{ $item->bookingFasilitas->map(fn($bf) => $bf->fasilitas->nama.' ('.$bf->jumlah.'x)')->implode(', ') }}
                        </div>
                    @endif
                </td>

                {{-- STATUS --}}
                <td class="py-4 px-3 text-center">
                    @php
                        $label = ['1'=>'Menunggu','2'=>'Disetujui','3'=>'Ditolak','4'=>'Selesai'][$item->status] ?? $item->status;
                        $color = [
                            '1' => 'bg-yellow-600',
                            '2' => 'bg-green-600',
                            '3' => 'bg-red-600',
                            '4' => 'bg-blue-600'
                        ][$item->status] ?? 'bg-gray-600';
                    @endphp

                    <span class="px-3 py-1 rounded text-white text-xs {{ $color }}">
                        {{ $label }}
                    </span>
                </td>

                {{-- AKSI --}}
                <td class="py-4 px-3">
                    <div class="flex justify-center gap-2 flex-wrap">

                        <a href="{{ route('admin.booking.edit', $item->id) }}"
                           class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs">
                            Edit
                        </a>

                        <a href="{{ route('admin.booking.invoice', $item->id) }}"
                           class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs">
                            Detail
                        </a>

                        <form action="{{ route('admin.booking.destroy', $item->id) }}" method="POST"
                              onsubmit="return confirm('Hapus jadwal ini?')">
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
                <td colspan="5" class="p-6 text-center text-gray-500">Tidak ada jadwal.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
