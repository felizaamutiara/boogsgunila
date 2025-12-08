@extends('admin.layout')

@section('content')
<h1 class="text-2xl font-bold mb-4">DEBUG: User List</h1>

<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-100">
        <tr>
            <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
            <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
            <th class="border border-gray-300 px-4 py-2 text-left">Email</th>
            <th class="border border-gray-300 px-4 py-2 text-left">Role</th>
            <th class="border border-gray-300 px-4 py-2 text-left">Email Verified</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
            <tr>
                <td class="border border-gray-300 px-4 py-2">{{ substr($user->id, 0, 8) }}...</td>
                <td class="border border-gray-300 px-4 py-2">{{ $user->name }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $user->email }}</td>
                <td class="border border-gray-300 px-4 py-2">
                    <span class="px-2 py-1 rounded text-white {{ $user->role === 'A' ? 'bg-red-500' : 'bg-blue-500' }}">
                        {{ $user->role === 'A' ? 'Admin' : 'User' }}
                    </span>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                    @if($user->email_verified_at)
                        <span class="px-2 py-1 rounded bg-green-100 text-green-800">✓ {{ $user->email_verified_at->format('Y-m-d H:i') }}</span>
                    @else
                        <span class="px-2 py-1 rounded bg-red-100 text-red-800">✗ Not Verified</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="mt-4 text-sm text-gray-600">
    User yang punya email_verified_at dapat langsung login tanpa OTP.
</p>
@endsection
