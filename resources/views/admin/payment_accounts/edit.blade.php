@extends('admin.layout')

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-bold">Edit Rekening Pembayaran</h1>
    <p class="text-sm text-gray-600">Perbarui data bank account atau e-wallet</p>
</div>

<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
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

    <form action="{{ route('admin.payment_accounts.update', $account->id) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-2">
                <span class="text-red-600">*</span> Tipe Pembayaran
            </label>
            <select name="type" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                <option value="bayar-ditempat" {{ $account->type === 'bayar-ditempat' ? 'selected' : '' }}>💰 Bayar di Tempat</option>
                <option value="transfer-bank" {{ $account->type === 'transfer-bank' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                <option value="e-wallet" {{ $account->type === 'e-wallet' ? 'selected' : '' }}>📱 E-Wallet</option>
            </select>
            @error('type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">
                <span class="text-red-600">*</span> Nama Rekening
            </label>
            <input type="text" name="name" value="{{ $account->name }}" placeholder="Contoh: Bank BCA, OVO" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">
                    <span class="text-red-600">*</span> Nomor Rekening/HP
                </label>
                <input type="text" name="account_number" value="{{ $account->account_number }}" placeholder="Contoh: 1234567890" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                @error('account_number')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">
                    <span class="text-red-600">*</span> Atas Nama
                </label>
                <input type="text" name="account_name" value="{{ $account->account_name }}" placeholder="Contoh: GSG Unila" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                @error('account_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Deskripsi (Opsional)</label>
            <textarea name="description" placeholder="Contoh: Transfer ke rekening BCA GSG Unila" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" rows="3">{{ $account->description }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ $account->is_active ? 'checked' : '' }} class="rounded">
                <span class="text-sm font-medium">Aktif (tampil di form pembayaran user)</span>
            </label>
        </div>

        <div class="flex gap-2 pt-3">
            <a href="{{ route('admin.payment_accounts.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>
</div>

@endsection
