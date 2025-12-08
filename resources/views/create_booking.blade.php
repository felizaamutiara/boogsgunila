@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <h1 class="text-2xl font-bold mb-6">Buat Booking Gedung</h1>
        <div class="bg-white shadow rounded p-6">
            <form action="{{ route('booking.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
                        <strong class="block font-semibold">Terjadi kesalahan:</strong>
                        <ul class="list-disc pl-5 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            <span class="text-red-600">*</span> Pilih Gedung
                        </label>
                        @if($gedung->count() > 0)
                            <select name="gedung_id" id="gedung_id" class="mt-1 w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">- Pilih Gedung -</option>
                                @foreach($gedung as $g)
                                    <option value="{{ $g->id }}" {{ old('gedung_id') == $g->id ? 'selected' : '' }} data-kapasitas="{{ $g->kapasitas }}" data-lokasi="{{ $g->lokasi ?? '-' }}" data-harga="{{ $g->harga ?? 0 }}">
                                        {{ $g->nama }} - Kapasitas: {{ $g->kapasitas }} orang @if($g->lokasi) ({{ $g->lokasi }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div id="gedung-info" class="mt-2 p-2 bg-blue-50 rounded text-sm text-gray-700 hidden">
                                <p><strong>Lokasi:</strong> <span id="gedung-lokasi">-</span></p>
                                <p><strong>Kapasitas Maksimal:</strong> <span id="gedung-kapasitas">-</span> orang</p>
                                <p><strong>Harga Sewa:</strong> <span id="gedung-harga">-</span></p>
                            </div>
                        @else
                            <div class="mt-1 p-4 bg-yellow-50 border border-yellow-200 rounded">
                                <p class="text-yellow-800 text-sm">
                                    <strong>⚠️ Belum ada gedung tersedia.</strong><br>
                                    Silakan hubungi admin untuk menambahkan gedung terlebih dahulu.
                                </p>
                            </div>
                            <select name="gedung_id" class="mt-1 w-full border rounded px-3 py-2 bg-gray-100" disabled>
                                <option value="">- Tidak ada gedung -</option>
                            </select>
                        @endif
                        @error('gedung_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const gedungSelect = document.getElementById('gedung_id');
                        const gedungInfo = document.getElementById('gedung-info');
                        const gedungLokasi = document.getElementById('gedung-lokasi');
                        const gedungKapasitas = document.getElementById('gedung-kapasitas');
                        const capacityInput = document.querySelector('input[name="capacity"]');
                        
                        if (gedungSelect) {
                            gedungSelect.addEventListener('change', function() {
                                const selectedOption = this.options[this.selectedIndex];
                                
                                if (this.value && selectedOption.dataset.kapasitas) {
                                    gedungLokasi.textContent = selectedOption.dataset.lokasi || '-';
                                    gedungKapasitas.textContent = selectedOption.dataset.kapasitas || '-';
                                    // show price nicely
                                    const gedungHargaEl = document.getElementById('gedung-harga');
                                    if (gedungHargaEl) {
                                        const raw = selectedOption.dataset.harga || 0;
                                        gedungHargaEl.textContent = 'Rp ' + Number(raw || 0).toLocaleString('id-ID');
                                    }
                                    gedungInfo.classList.remove('hidden');
                                    
                                    // Set max capacity
                                    if (capacityInput) {
                                        capacityInput.setAttribute('max', selectedOption.dataset.kapasitas);
                                    }
                                } else {
                                    gedungInfo.classList.add('hidden');
                                    if (capacityInput) {
                                        capacityInput.removeAttribute('max');
                                    }
                                }
                                recalcTotal();
                            });
                            
                            // Trigger on page load if value already selected
                            if (gedungSelect.value) {
                                gedungSelect.dispatchEvent(new Event('change'));
                            }
                        }
                    });
                </script>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Nama Acara</label>
                        <input type="text" name="event_name" value="{{ old('event_name') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                        @error('event_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Jenis Acara</label>
                        <input type="text" name="event_type" value="{{ old('event_type') }}" class="mt-1 w-full border rounded px-3 py-2" required>
                        @error('event_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">Kapasitas</label>
                        <input type="number" name="capacity" value="{{ old('capacity', request('capacity')) }}" class="mt-1 w-full border rounded px-3 py-2" min="1" max="10000" required oninput="validateCapacity(this)">
                        <p id="capacity-error" class="text-red-600 text-sm mt-1 hidden"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Nomor Telepon Pemesan</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full border rounded px-3 py-2" placeholder="0812xxxx" required oninput="validatePhone(this)">
                        <p id="phone-error" class="text-red-600 text-sm mt-1 hidden"></p>
                        @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                
                <script>
                    function validateCapacity(input) {
                        const errorEl = document.getElementById('capacity-error');
                        const capacity = parseInt(input.value);
                        const gedungSelect = document.getElementById('gedung_id');
                        const selectedOption = gedungSelect.options[gedungSelect.selectedIndex];
                        
                        if (selectedOption && selectedOption.dataset.kapasitas) {
                            const maxKapasitas = parseInt(selectedOption.dataset.kapasitas);
                            if (capacity > maxKapasitas) {
                                errorEl.textContent = `Kapasitas tidak boleh melebihi ${maxKapasitas} orang (kapasitas gedung)`;
                                errorEl.classList.remove('hidden');
                                input.classList.add('border-red-600');
                            } else {
                                errorEl.classList.add('hidden');
                                input.classList.remove('border-red-600');
                            }
                        }
                    }
                    
                    function validatePhone(input) {
                        const errorEl = document.getElementById('phone-error');
                        const phone = input.value.trim();
                        
                        if (phone && !/^(\+62|62|0)[0-9]{9,12}$/.test(phone)) {
                            errorEl.textContent = 'Format nomor telepon tidak valid (mulai dengan 0 atau +62)';
                            errorEl.classList.remove('hidden');
                            input.classList.add('border-red-600');
                        } else {
                            errorEl.classList.add('hidden');
                            input.classList.remove('border-red-600');
                        }
                    }
                </script>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">
                            <span class="text-red-600">*</span> Tanggal Mulai
                        </label>
                        <input type="date" name="date" id="date_start" value="{{ old('date', request('date')) }}" class="mt-1 w-full border rounded px-3 py-2" required>
                        @error('date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Tanggal Selesai (opsional)</label>
                        <input type="date" name="end_date" id="date_end" value="{{ old('end_date', request('end_date')) }}" class="mt-1 w-full border rounded px-3 py-2" min="">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika hanya 1 hari</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium">
                            <span class="text-red-600">*</span> Jam Mulai
                        </label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', request('start_time')) }}" class="mt-1 w-full border rounded px-3 py-2" required oninput="validateTimes()">
                        <p id="time-error" class="text-red-600 text-sm mt-1 hidden"></p>
                        @error('start_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">
                            <span class="text-red-600">*</span> Jam Selesai
                        </label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', request('end_time')) }}" class="mt-1 w-full border rounded px-3 py-2" required oninput="validateTimes()">
                        @error('end_time')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                
                <script>
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
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const dateStart = document.getElementById('date_start');
                        const dateEnd = document.getElementById('date_end');
                        
                        dateStart.addEventListener('change', function() {
                            if (dateEnd) {
                                dateEnd.setAttribute('min', this.value);
                                if (dateEnd.value && dateEnd.value < this.value) {
                                    dateEnd.value = this.value;
                                }
                            }
                        });
                    });
                </script>
                <div>
                    <label class="block text-sm font-medium">Proposal (PDF/DOC)</label>
                    <input type="file" name="proposal_file" accept=".pdf,.doc,.docx" class="mt-1 w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Fasilitas (opsional)</label>
                    <!-- Stock validation error -->
                    <div id="stockError" class="hidden mb-3 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm"></div>
                    
                    <div class="space-y-2">
                           @foreach($fasilitas as $f)
                            <div class="flex items-center gap-3">
                                <input type="checkbox" 
                                    id="f_{{ $f->id }}" 
                                    data-harga="{{ $f->harga ?? 0 }}"
                                    data-stok="{{ $f->stok }}"
                                    data-nama="{{ $f->nama }}"
                                    onchange="toggleFasilitas(this, {{ $loop->index }}); recalcTotal();"
                                    @if($f->stok <= 0) disabled @endif>
                                <label for="f_{{ $f->id }}" class="w-48 flex-1">
                                    {{ $f->nama }} - Rp {{ number_format($f->harga ?? 0, 0, ',', '.') }} 
                                    <span class="text-xs text-gray-500">(Stok: {{ $f->stok }})</span>
                                    @if($f->stok <= 0)
                                        <span class="text-xs text-red-600 font-semibold ml-2">❌ Stok Kosong</span>
                                    @endif
                                </label>
                                <input type="hidden" 
                                    name="fasilitas[{{ $loop->index }}][id]" 
                                    value="{{ $f->id }}" 
                                    id="fasilitas_id_{{ $loop->index }}" 
                                    disabled>
                                <input type="number" 
                                    name="fasilitas[{{ $loop->index }}][jumlah]" 
                                    class="border rounded px-2 py-1 w-24" 
                                    min="1" 
                                    max="{{ $f->stok }}" 
                                    value="1" 
                                    id="fasilitas_jumlah_{{ $loop->index }}" 
                                    disabled
                                    oninput="recalcTotal()">
                            </div>
                           @endforeach

                        <script>
                            function toggleFasilitas(checkbox, index) {
                                const idField = document.getElementById('fasilitas_id_' + index);
                                const jumlahField = document.getElementById('fasilitas_jumlah_' + index);
                                const stok = parseInt(checkbox.dataset.stok || 0);
                                
                                // Check if stock is empty
                                if (stok <= 0) {
                                    checkbox.checked = false;
                                    showStockError(checkbox.dataset.nama + ' tidak memiliki stok');
                                    return;
                                }
                                
                                if (checkbox.checked) {
                                    idField.disabled = false;
                                    jumlahField.disabled = false;
                                } else {
                                    idField.disabled = true;
                                    jumlahField.disabled = true;
                                }
                                
                                // Clear error when all stocks are ok
                                validateAllStocks();
                            }
                            
                            function showStockError(message) {
                                const errorEl = document.getElementById('stockError');
                                errorEl.textContent = '⚠️ ' + message;
                                errorEl.classList.remove('hidden');
                            }
                            
                            function hideStockError() {
                                const errorEl = document.getElementById('stockError');
                                errorEl.classList.add('hidden');
                            }
                            
                            function validateAllStocks() {
                                const checkboxes = document.querySelectorAll('input[type="checkbox"][data-stok]');
                                let hasError = false;
                                
                                checkboxes.forEach(cb => {
                                    if (cb.checked) {
                                        const stok = parseInt(cb.dataset.stok || 0);
                                        if (stok <= 0) {
                                            hasError = true;
                                            showStockError(cb.dataset.nama + ' tidak memiliki stok');
                                            return;
                                        }
                                    }
                                });
                                
                                if (!hasError) {
                                    hideStockError();
                                }
                            }
                        </script>
                    </div>
                    @error('fasilitas')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('fasilitas.*.id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    @error('fasilitas.*.jumlah')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-2 pt-2">
                    <a href="{{ route('booking.index') }}" class="px-4 py-2 border rounded">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                </div>
                <div class="mt-4 bg-gray-50 p-4 rounded border">
                    <h3 class="font-semibold mb-2">Ringkasan Biaya</h3>
                    <div class="text-sm text-gray-700">
                        <p>Gedung: <span id="gedung-price">Rp 0</span></p>
                        <p>Fasilitas: <span id="fasilitas-subtotal">Rp 0</span></p>
                        <p class="font-bold">Total: <span id="total-price">Rp 0</span></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    <script>
        function formatRupiah(number) {
            return 'Rp ' + (Number(number) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function recalcTotal() {
            const gedungSelect = document.getElementById('gedung_id');
            let gedungPrice = 0;
            if (gedungSelect && gedungSelect.options && gedungSelect.options[gedungSelect.selectedIndex]) {
                gedungPrice = parseInt(gedungSelect.options[gedungSelect.selectedIndex].dataset.harga || 0);
            }

            let fasilitasSubtotal = 0;
            @foreach($fasilitas as $f)
                (function(){
                    const checkbox = document.getElementById('f_{{ $f->id }}');
                    const jumlah = document.getElementById('fasilitas_jumlah_{{ $loop->index }}');
                    if (checkbox && checkbox.checked) {
                        const harga = parseInt(checkbox.dataset.harga || 0);
                        const qty = jumlah ? parseInt(jumlah.value || 0) : 0;
                        fasilitasSubtotal += harga * qty;
                    }
                })();
            @endforeach

            const total = (gedungPrice || 0) + fasilitasSubtotal;
            const gedungPriceEl = document.getElementById('gedung-price');
            const fasilitasSubtotalEl = document.getElementById('fasilitas-subtotal');
            const totalPriceEl = document.getElementById('total-price');

            if (gedungPriceEl) gedungPriceEl.textContent = formatRupiah(gedungPrice || 0);
            if (fasilitasSubtotalEl) fasilitasSubtotalEl.textContent = formatRupiah(fasilitasSubtotal || 0);
            if (totalPriceEl) totalPriceEl.textContent = formatRupiah(total || 0);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const gedungSelect = document.getElementById('gedung_id');
            if (gedungSelect) gedungSelect.addEventListener('change', function() { recalcTotal(); });

            // Recalculate when user toggles fasilitas checkboxes via keyboard/click
            @foreach($fasilitas as $f)
                (function(){
                    const cb = document.getElementById('f_{{ $f->id }}');
                    const qty = document.getElementById('fasilitas_jumlah_{{ $loop->index }}');
                    if (cb) cb.addEventListener('change', recalcTotal);
                    if (qty) qty.addEventListener('input', recalcTotal);
                })();
            @endforeach

            // Initial calculation
            recalcTotal();
        });
    </script>

    @endsection


