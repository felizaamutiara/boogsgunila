@extends('admin.layout')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Edit Jadwal (Admin)</h1>
    <p class="text-sm text-gray-600">Perbarui data booking dan status</p>
</div>

<div class="bg-white rounded-xl shadow p-6">
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
            <strong>Terjadi kesalahan:</strong>
            <ul class="list-disc list-inside mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.booking.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf @method('PUT')
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Pilih Pemesan (User)</label>
                <select name="user_id" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Pemesan --</option>
                    @foreach($gedung as $g) @break @endforeach
                    @foreach(\App\Models\User::orderBy('name')->get() as $u)
                        <option value="{{ $u->id }}" {{ ($item->user_id == $u->id) ? 'selected' : '' }}>{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Pilih Gedung</label>
                <select name="gedung_id" class="mt-1 w-full border rounded px-3 py-2" required>
                    <option value="">-- Pilih Gedung --</option>
                    @foreach($gedung as $g)
                        <option value="{{ $g->id }}" {{ ($item->gedung_id == $g->id) ? 'selected' : '' }}>{{ $g->nama }} @if($g->lokasi) ({{ $g->lokasi }}) @endif</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Nama Acara</label>
                <input type="text" name="event_name" value="{{ $item->event_name }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Jenis Acara</label>
                <input type="text" name="event_type" value="{{ $item->event_type }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium">Kapasitas</label>
                <input type="number" name="capacity" min="1" value="{{ $item->capacity }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Nomor Telepon Pemesan</label>
                <input type="text" name="phone" value="{{ $item->phone ?? ($item->user->phone ?? '') }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Tanggal Mulai</label>
                <input type="date" name="date" value="{{ $item->date }}" class="mt-1 w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Tanggal Selesai (opsional)</label>
                <input type="date" name="end_date" value="{{ $item->end_date }}" class="mt-1 w-full border rounded px-3 py-2">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-medium">Jam Mulai</label>
                    <input type="time" name="start_time" value="{{ $item->start_time }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Jam Selesai</label>
                    <input type="time" name="end_time" value="{{ $item->end_time }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <label class="block text-sm font-semibold text-gray-800 mb-3">Ubah Status Booking</label>
            <div class="grid grid-cols-4 gap-2">
                <label class="flex flex-col items-center p-3 border-2 rounded cursor-pointer transition hover:bg-yellow-50" style="border-color: {{ $item->status === '1' ? '#fbbf24' : '#e5e7eb' }};background-color: {{ $item->status === '1' ? '#fffbeb' : 'white' }};">
                    <input type="radio" name="status" value="1" class="mb-1" {{ $item->status==='1' ? 'checked' : '' }} required>
                    <span class="text-xs font-medium text-center">Menunggu</span>
                </label>
                <label class="flex flex-col items-center p-3 border-2 rounded cursor-pointer transition hover:bg-green-50" style="border-color: {{ $item->status === '2' ? '#22c55e' : '#e5e7eb' }};background-color: {{ $item->status === '2' ? '#f0fdf4' : 'white' }};">
                    <input type="radio" name="status" value="2" class="mb-1" {{ $item->status==='2' ? 'checked' : '' }} required>
                    <span class="text-xs font-medium text-center">Disetujui</span>
                </label>
                <label class="flex flex-col items-center p-3 border-2 rounded cursor-pointer transition hover:bg-red-50" style="border-color: {{ $item->status === '3' ? '#ef4444' : '#e5e7eb' }};background-color: {{ $item->status === '3' ? '#fef2f2' : 'white' }};">
                    <input type="radio" name="status" value="3" class="mb-1" {{ $item->status==='3' ? 'checked' : '' }} required>
                    <span class="text-xs font-medium text-center">Ditolak</span>
                </label>
                <label class="flex flex-col items-center p-3 border-2 rounded cursor-pointer transition hover:bg-blue-50" style="border-color: {{ $item->status === '4' ? '#3b82f6' : '#e5e7eb' }};background-color: {{ $item->status === '4' ? '#eff6ff' : 'white' }};">
                    <input type="radio" name="status" value="4" class="mb-1" {{ $item->status==='4' ? 'checked' : '' }} required>
                    <span class="text-xs font-medium text-center">Selesai</span>
                </label>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Proposal Saat Ini</label>
            @if($item->proposal_file)
                <div class="mt-1">
                    <a href="{{ asset('storage/'.$item->proposal_file) }}" target="_blank" class="text-blue-700 underline">Lihat proposal</a>
                </div>
            @else
                <div class="text-gray-500">Tidak ada proposal</div>
            @endif
            <div class="mt-2">
                <label class="block text-sm font-medium">Unggah Proposal Baru (opsional)</label>
                <input type="file" name="proposal_file" accept=".pdf,.doc,.docx" class="mt-1 w-full">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium">Fasilitas (opsional)</label>
            <div class="mt-2 space-y-2">
                @php $idx = 0; @endphp
                @foreach($fasilitas as $f)
                    @php
                        $bf = $item->bookingFasilitas->firstWhere('fasilitas_id', $f->id);
                    @endphp
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="f_{{ $f->id }}" 
                            class="facility-check" 
                            data-idx="{{ $idx }}"
                            data-facility-id="{{ $f->id }}"
                            {{ $bf ? 'checked' : '' }} 
                            onchange="toggleFacility(this)">
                        <label for="f_{{ $f->id }}" class="flex-1 text-sm font-medium cursor-pointer">
                            {{ $f->nama }} (Rp {{ number_format($f->harga ?? 0) }})
                        </label>
                        <input type="hidden" name="fasilitas[{{ $idx }}][id]" value="{{ $f->id }}" {{ $bf ? '' : 'disabled' }}>
                        <input type="number" name="fasilitas[{{ $idx }}][jumlah]" min="1" value="{{ $bf->jumlah ?? 1 }}" class="w-20 border rounded px-2 py-1" {{ $bf ? '' : 'disabled' }}>
                    </div>
                    @php $idx++; @endphp
                @endforeach
            </div>
        </div>

        <script>
            function toggleFacility(checkbox) {
                const idx = checkbox.getAttribute('data-idx');
                const hiddenInput = document.querySelector(`input[name="fasilitas[${idx}][id]"]`);
                const qtyInput = document.querySelector(`input[name="fasilitas[${idx}][jumlah]"]`);
                
                if (checkbox.checked) {
                    hiddenInput.disabled = false;
                    qtyInput.disabled = false;
                } else {
                    hiddenInput.disabled = true;
                    qtyInput.disabled = true;
                }
            }
        </script>

        <div class="flex gap-2 pt-3">
            <a href="{{ route('admin.schedules.index') }}" class="px-4 py-2 border rounded">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan Perubahan</button>
        </div>
    </form>
</div>

@endsection
