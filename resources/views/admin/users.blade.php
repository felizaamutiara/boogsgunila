@extends('admin.layout')

@section('content')
<div class="flex items-center justify-between mb-4">
	<h1 class="text-2xl font-extrabold text-gray-800">Data Pengguna</h1>
	<a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded">+ Tambah Pengguna</a>
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
				<th class="p-3">Nama</th>
				<th class="p-3">Email</th>
				<th class="p-3">Role</th>
				<th class="p-3">Aksi</th>
			</tr>
		</thead>
		<tbody>
			@forelse($users as $i => $u)
			<tr class="border-t">
				<td class="p-3">{{ $i+1 }}</td>
				<td class="p-3">{{ $u->name }}</td>
				<td class="p-3 text-gray-600">{{ $u->email }}</td>
				<td class="p-3">
					<span class="px-2 py-1 rounded-full text-white text-xs {{ $u->role==='A' ? 'bg-blue-600' : 'bg-gray-600' }}">
						{{ $u->role==='A' ? 'Admin' : 'User' }}
					</span>
				</td>
				<td class="p-3">
					<div class="flex gap-2">
						<a href="{{ route('admin.users.edit', $u->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded text-xs">Edit</a>
						@if($u->id !== auth()->id())
						<form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
							@csrf @method('DELETE')
							<button class="px-3 py-1 bg-red-600 text-white rounded text-xs">Hapus</button>
						</form>
						@endif
					</div>
				</td>
			</tr>
			@empty
			<tr>
				<td colspan="5" class="p-6 text-center text-gray-500">Tidak ada pengguna.</td>
			</tr>
			@endforelse
		</tbody>
	</table>
</div>
@endsection


