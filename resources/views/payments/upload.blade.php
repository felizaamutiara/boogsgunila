@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Upload Bukti Pembayaran</h1>
    <p class="text-gray-600 mb-6">Acara: <strong>{{ $booking->event_name }}</strong></p>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid md:grid-cols-3 gap-6">
        <!-- Payment Info -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pembayaran</h2>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Acara:</span>
                        <span class="font-semibold">{{ $booking->event_name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Gedung:</span>
                        <span class="font-semibold">{{ $booking->gedung->nama ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Tanggal:</span>
                        @php
                            $start = \Carbon\Carbon::parse($booking->date);
                            $end = $booking->end_date ? \Carbon\Carbon::parse($booking->end_date) : null;
                        @endphp
                        <span>
                            @if($end && $end->ne($start))
                                Dari {{ $start->format('d M Y') }} sampai {{ $end->format('d M Y') }}
                            @else
                                {{ $start->format('d M Y') }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Total Pembayaran:</span>
                        <span class="font-bold text-lg text-blue-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Pilih Metode Pembayaran</h2>
                
                <form id="paymentForm" method="POST" action="{{ route('payments.upload', $payment->id) }}" enctype="multipart/form-data" class="space-y-4" onsubmit="handleFormSubmit(event)">
                    @csrf

                    <!-- Error Alert Box -->
                    <div id="errorAlert" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <p class="font-semibold mb-2">⚠️ Terjadi Kesalahan:</p>
                        <ul id="errorList" class="list-disc list-inside space-y-1 text-sm"></ul>
                    </div>

                    <!-- Payment Methods -->
                    <div class="space-y-3">
                        <!-- Bayar di Tempat -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition">
                            <input type="radio" name="selected_method" value="bayar-ditempat" checked class="mt-1 mr-3" onchange="updatePaymentUI()">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">Bayar di Tempat</p>
                                <p class="text-sm text-gray-600">Bayar langsung saat acara</p>
                            </div>
                        </label>

                        <!-- Transfer Bank -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition">
                            <input type="radio" name="selected_method" value="transfer-bank" class="mt-1 mr-3" onchange="updatePaymentUI()">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">Transfer Bank</p>
                                <p class="text-sm text-gray-600">Transfer ke rekening yang disediakan</p>
                            </div>
                        </label>

                        <!-- E-Wallet -->
                        <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 transition">
                            <input type="radio" name="selected_method" value="e-wallet" class="mt-1 mr-3" onchange="updatePaymentUI()">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">E-Wallet</p>
                                <p class="text-sm text-gray-600">Pembayaran melalui e-wallet</p>
                            </div>
                        </label>
                    </div>

                    <!-- Account Selection (hidden initially) -->
                    <div id="accountSection" class="hidden space-y-3">
                        <p class="font-semibold text-gray-800">Pilih Rekening Tujuan</p>
                        <select name="payment_account_id" id="paymentAccount" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Rekening --</option>
                            @foreach($paymentAccounts as $acc)
                                <option value="{{ $acc->id }}" data-type="{{ $acc->type }}">
                                    {{ $acc->name }} - {{ $acc->account_number }} ({{ $acc->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('payment_account_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Proof File Upload -->
                    <div id="proofSection" class="hidden space-y-3">
                        <p class="font-semibold text-gray-800">Upload Bukti Pembayaran</p>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition" onclick="document.getElementById('proofFile').click()">
                            <div id="fileInfo" class="hidden">
                                <p class="text-sm text-gray-600">File terpilih:</p>
                                <p id="fileName" class="font-semibold text-blue-600"></p>
                            </div>
                            <div id="filePlaceholder">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-8-12v8m0 0l-4-4m4 4l4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-600">Klik untuk upload atau drag file</p>
                                <p class="text-xs text-gray-500">JPG, PNG, PDF (Max 4MB)</p>
                            </div>
                        </div>
                        <input type="file" id="proofFile" name="proof" accept=".jpg,.jpeg,.png,.pdf" class="hidden" onchange="updateFileName()">
                        @error('proof')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
                            Konfirmasi Pembayaran
                        </button>
                        <a href="{{ route('booking.invoice', $booking->id) }}" class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 rounded-lg text-center hover:bg-gray-50 transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Card -->
        <div>
            <div class="bg-blue-50 rounded-lg shadow p-6 sticky top-20">
                <h3 class="font-semibold text-gray-800 mb-4">Ringkasan</h3>
                
                <div class="space-y-3 mb-4 pb-4 border-b">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total:</span>
                        <span class="font-bold text-blue-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 rounded text-white text-xs font-semibold bg-yellow-600">Belum Dibayar</span>
                    </div>
                </div>

                <div class="bg-blue-100 border border-blue-300 rounded p-3 text-sm text-blue-900">
                    <p><strong>⚠️ Penting!</strong></p>
                    <p class="mt-2">Setelah submit, admin akan memverifikasi pembayaran Anda. Proses verifikasi biasanya memakan waktu 1-2 jam.</p>
                </div>

                <!-- Contact Info -->
                <div class="mt-6 text-sm text-gray-600 border-t pt-4">
                    <p class="font-semibold text-gray-800 mb-2">Butuh Bantuan?</p>
                    <p>Hubungi admin untuk bantuan pembayaran atau pertanyaan teknis.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePaymentUI() {
    const selectedMethod = document.querySelector('input[name="selected_method"]:checked').value;
    const accountSection = document.getElementById('accountSection');
    const proofSection = document.getElementById('proofSection');
    const paymentAccountSelect = document.getElementById('paymentAccount');
    
    // Determine which account types to show based on payment method
    let accountTypeToShow = null;
    
    if (selectedMethod === 'bayar-ditempat') {
        accountSection.classList.add('hidden');
        proofSection.classList.add('hidden');
    } else if (selectedMethod === 'transfer-bank') {
        accountSection.classList.remove('hidden');
        proofSection.classList.remove('hidden');
        accountTypeToShow = 'transfer-bank';
    } else if (selectedMethod === 'e-wallet') {
        accountSection.classList.remove('hidden');
        proofSection.classList.remove('hidden');
        accountTypeToShow = 'e-wallet';
    }
    
    // Filter account options based on type
    const allOptions = paymentAccountSelect.querySelectorAll('option');
    allOptions.forEach(option => {
        if (option.value === '') {
            // Always show placeholder
            option.style.display = 'block';
        } else if (accountTypeToShow) {
            const optionType = option.dataset.type || '';
            if (optionType.toLowerCase() === accountTypeToShow.toLowerCase()) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    // Reset the select value if current selection is hidden
    if (paymentAccountSelect.value) {
        const selectedOption = paymentAccountSelect.querySelector(`option[value="${paymentAccountSelect.value}"]`);
        if (selectedOption && selectedOption.style.display === 'none') {
            paymentAccountSelect.value = '';
        }
    }
}

function updateFileName() {
    const fileInput = document.getElementById('proofFile');
    const fileName = document.getElementById('fileName');
    const fileInfo = document.getElementById('fileInfo');
    const filePlaceholder = document.getElementById('filePlaceholder');

    if (fileInput.files.length > 0) {
        fileName.textContent = fileInput.files[0].name;
        fileInfo.classList.remove('hidden');
        filePlaceholder.classList.add('hidden');
    } else {
        fileInfo.classList.add('hidden');
        filePlaceholder.classList.remove('hidden');
    }
}

// Initialize UI
document.addEventListener('DOMContentLoaded', function() {
    updatePaymentUI();
});

function handleFormSubmit(event) {
    event.preventDefault();
    
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    const selectedMethod = document.querySelector('input[name="selected_method"]:checked').value;

    // Validate based on selected method
    const errorAlert = document.getElementById('errorAlert');
    const errorList = document.getElementById('errorList');
    errorList.innerHTML = '';
    errorAlert.classList.add('hidden');

    let hasError = false;

    if (selectedMethod !== 'bayar-ditempat') {
        if (!document.getElementById('paymentAccount').value) {
            errorList.innerHTML += '<li>Pilih rekening tujuan terlebih dahulu</li>';
            hasError = true;
        }

        const fileInput = document.getElementById('proofFile');
        if (!fileInput.files.length) {
            errorList.innerHTML += '<li>Upload bukti pembayaran terlebih dahulu</li>';
            hasError = true;
        }
    }

    if (hasError) {
        errorAlert.classList.remove('hidden');
        window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
        return;
    }

    // Submit form via fetch to handle validation errors
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.href = response.url || '{{ route("payments.index") }}';
        } else if (response.status === 422) {
            return response.json().then(data => {
                errorList.innerHTML = '';
                if (data.errors) {
                    Object.entries(data.errors).forEach(([field, messages]) => {
                        messages.forEach(msg => {
                            errorList.innerHTML += '<li>' + msg + '</li>';
                        });
                    });
                }
                errorAlert.classList.remove('hidden');
                window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
            });
        } else {
            errorList.innerHTML = '<li>Terjadi kesalahan server. Silakan coba lagi.</li>';
            errorAlert.classList.remove('hidden');
        }
    })
    .catch(error => {
        errorList.innerHTML = '<li>Terjadi kesalahan jaringan. Silakan coba lagi.</li>';
        errorAlert.classList.remove('hidden');
    });
}
</script>
@endsection
