@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Booking</h1>
    <p class="text-sm text-gray-600 mb-6">Perbarui data booking Anda</p>

    @if($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('booking.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Gedung</label>
                    <select name="gedung_id" id="gedung_id" class="mt-1 w-full border rounded px-3 py-2" required>
                        <option value="">- Pilih Gedung -</option>
                        @foreach($gedung as $g)
                            <option value="{{ $g->id }}" {{ ($item->gedung_id == $g->id) ? 'selected' : '' }}>
                                {{ $g->nama }} @if($g->lokasi) ({{ $g->lokasi }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium">Nama Acara</label>
                    <input type="text" name="event_name" value="{{ $item->event_name }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Jenis Acara</label>
                    <input type="text" name="event_type" value="{{ $item->event_type }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Kapasitas</label>
                    <input type="number" name="capacity" min="1" value="{{ $item->capacity }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ $item->phone }}" class="mt-1 w-full border rounded px-3 py-2" required placeholder="0812xxxx">
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Tanggal Mulai</label>
                    <input type="date" name="date" value="{{ $item->date }}" class="mt-1 w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium">Tanggal Selesai (opsional)</label>
                    <input type="date" name="end_date" value="{{ $item->end_date }}" class="mt-1 w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Jam Mulai</label>
                    <input type="time" name="start_time" id="start_time" value="{{ $item->start_time }}" class="mt-1 w-full border rounded px-3 py-2" required oninput="validateTimes()">
                </div>
                <div>
                    <label class="block text-sm font-medium">Jam Selesai</label>
                    <input type="time" name="end_time" id="end_time" value="{{ $item->end_time }}" class="mt-1 w-full border rounded px-3 py-2" required oninput="validateTimes()">
                </div>
            </div>
            <p id="time-error" class="text-red-600 text-sm mt-1 hidden"></p>

            <div>
                <label class="block text-sm font-medium">Proposal Saat Ini</label>
                @if($item->proposal_file)
                    <div class="mt-1 mb-2">
                        <a href="{{ asset('storage/'.$item->proposal_file) }}" target="_blank" class="text-blue-700 underline">✓ Lihat proposal</a>
                    </div>
                @else
                    <div class="text-gray-500 text-sm mt-1 mb-2">Tidak ada proposal</div>
                @endif
                <label class="block text-sm font-medium mt-3">Unggah Proposal Baru (opsional)</label>
                <input type="file" name="proposal_file" accept=".pdf,.doc,.docx" class="mt-1 w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Fasilitas (opsional)</label>
                <div class="space-y-2">
                    @php $selectedIds = $item->bookingFasilitas->pluck('fasilitas_id')->toArray(); @endphp
                    @foreach($fasilitas as $f)
                        @php $isSelected = in_array($f->id, $selectedIds); @endphp
                        <div class="flex items-center gap-3">
                            <input type="checkbox" 
                                   id="f_{{ $f->id }}" 
                                   onchange="toggleFasilitas(this, {{ $loop->index }})"
                                   {{ $isSelected ? 'checked' : '' }}>
                            <label for="f_{{ $f->id }}" class="w-48">{{ $f->nama }} (Stok: {{ $f->stok }})</label>
                            <input type="hidden" 
                                   name="fasilitas[{{ $loop->index }}][id]" 
                                   value="{{ $f->id }}" 
                                   id="fasilitas_id_{{ $loop->index }}" 
                                   {{ !$isSelected ? 'disabled' : '' }}>
                            <input type="number" 
                                   name="fasilitas[{{ $loop->index }}][jumlah]" 
                                   class="border rounded px-2 py-1 w-24" 
                                   min="1" 
                                   max="{{ $f->stok }}" 
                                   value="{{ $isSelected ? ($item->bookingFasilitas->where('fasilitas_id', $f->id)->first()->jumlah ?? 1) : 1 }}"
                                   id="fasilitas_jumlah_{{ $loop->index }}"
                                   {{ !$isSelected ? 'disabled' : '' }}>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-2 pt-4 border-t">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                <a href="{{ route('booking.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFasilitas(checkbox, index) {
        const idField = document.getElementById('fasilitas_id_' + index);
        const jumlahField = document.getElementById('fasilitas_jumlah_' + index);
        
        if (checkbox.checked) {
            idField.disabled = false;
            jumlahField.disabled = false;
        } else {
            idField.disabled = true;
            jumlahField.disabled = true;
        }
    }
    
    function validateTimes() {
        const startInput = document.getElementById('start_time');
        const endInput = document.getElementById('end_time');
        const errorEl = document.getElementById('time-error');
        
        if (startInput.value && endInput.value) {
            const [startH, startM] = startInput.value.split(':');
            const [endH, endM] = endInput.value.split(':');
            
            const startMins = parseInt(startH) * 60 + parseInt(startM);
            const endMins = parseInt(endH) * 60 + parseInt(endM);
            
            if (endMins <= startMins) {
                errorEl.textContent = 'Jam selesai harus lebih besar dari jam mulai';
                errorEl.classList.remove('hidden');
                endInput.classList.add('border-red-600');
            } else if (endMins - startMins < 60) {
                errorEl.textContent = 'Durasi minimal 1 jam';
                errorEl.classList.remove('hidden');
                endInput.classList.add('border-red-600');
            } else {
                errorEl.classList.add('hidden');
                endInput.classList.remove('border-red-600');
            }
        }
    }
</script>
@endsection
