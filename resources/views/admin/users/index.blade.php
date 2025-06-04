@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Manajemen Pengguna</h2>

        <!-- User List -->
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow">
            <thead>
                <tr>
                    <th class="py-2 px-4 text-left">Nama</th>
                    <th class="py-2 px-4 text-left">Email</th>
                    <th class="py-2 px-4 text-left">Status</th>
                    <th class="py-2 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="py-2 px-4">{{ $user->name }}</td>
                        <td class="py-2 px-4">{{ $user->email }}</td>
                        <td class="py-2 px-4">
                            <span class="{{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-2 px-4">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="text-blue-500">Lihat</a>
                            <form action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('POST')
                                <button type="submit" name="status" value="{{ $user->is_active ? 0 : 1 }}"
                                    class="text-sm text-yellow-500 hover:underline">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
