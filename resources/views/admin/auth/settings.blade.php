@extends('layouts.admin')

@section('content')
    <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Pengaturan Akun</h2>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1 font-medium">Nama</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                    class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                    class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-medium">Password (biarkan kosong jika tidak ingin diubah)</label>
                <input type="password" name="password" class="w-full border px-3 py-2 rounded">
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="mb-4">
                    <label class="block mb-1 font-medium">Role</label>
                    <select name="role" class="w-full border px-3 py-2 rounded">
                        <option value="admin" {{ auth()->user()->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="customer" {{ auth()->user()->role === 'customer' ? 'selected' : '' }}>Customer
                        </option>
                        <option value="courier" {{ auth()->user()->role === 'courier' ? 'selected' : '' }}>Courier</option>
                    </select>
                </div>
            @endif

            <button type="submit" class="bg-[#5979f5] hover:bg-[#2c3c84] text-white px-4 py-2 rounded transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
@endsection
