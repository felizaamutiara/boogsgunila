@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
  <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Verifikasi Email</h1>
    @if (session('message'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('message') }}</div>
    @endif
    <p class="text-gray-700 mb-4">Sebelum melanjutkan, silakan cek email Anda untuk link verifikasi. Jika belum menerima email, klik tombol di bawah untuk kirim ulang.</p>
    <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
      @csrf
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Kirim Ulang Email Verifikasi</button>
    </form>
  </div>
</div>
@endsection