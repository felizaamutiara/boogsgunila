@extends('layouts.app')

@section('content')
<section class="bg-white py-10">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<h1 class="text-3xl md:text-4xl font-extrabold text-blue-900 text-center">Jadwal Acara di GSG Unila</h1>
		<p class="text-gray-600 text-center">Lihat daftar acara yang telah terjadwal di Gedung Serba Guna Universitas Lampung.</p>

		<form method="GET" class="bg-blue-50 border rounded-xl p-4 mt-6 flex flex-wrap gap-3 items-center">
			<span class="text-gray-700 font-medium">Filter Jadwal Berdasarkan:</span>
			<select name="type" class="border rounded px-3 py-2">
				@php $type = $filter_type ?? 'all'; @endphp
				<option value="all" {{ $type==='all'?'selected':'' }}>Semua Acara</option>
				<option value="wisuda" {{ $type==='wisuda'?'selected':'' }}>Wisuda</option>
				<option value="konser" {{ $type==='konser'?'selected':'' }}>Konser</option>
				<option value="workshop" {{ $type==='workshop'?'selected':'' }}>Workshop</option>
				<option value="kampus" {{ $type==='kampus'?'selected':'' }}>Acara Kampus</option>
			</select>
			<input type="date" name="date" value="{{ $filter_date }}" class="border rounded px-3 py-2">
			<button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Terapkan</button>
		</form>

		<div class="grid md:grid-cols-3 gap-6 mt-6">
			@forelse($items as $b)
				<div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
					<img src="{{ asset('img/gsgjauh.jpg') }}" class="w-full h-40 object-cover" alt="banner">
					<div class="p-4">
						@php
							$startDate = \Carbon\Carbon::parse($b->date);
							$endDate = $b->end_date ? \Carbon\Carbon::parse($b->end_date) : null;
						@endphp
						<p class="text-xs text-blue-700 font-semibold">
							@if($endDate && $endDate->ne($startDate))
								Dari {{ $startDate->format('d F Y') }} sampai {{ $endDate->format('d F Y') }}
							@else
								{{ $startDate->format('d F Y') }}
							@endif
						</p>
						<h3 class="font-bold text-blue-900">{{ $b->event_name }}</h3>
						<p class="text-sm text-gray-600">{{ ucfirst($b->event_type) }} • {{ $b->start_time }} - {{ $b->end_time }}</p>
					</div>
				</div>
			@empty
				<p class="text-gray-600">Belum ada jadwal yang sesuai filter.</p>
			@endforelse
		</div>
	</div>
</section>
@endsection


