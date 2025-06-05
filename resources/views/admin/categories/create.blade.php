@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Tambah Kategori</h2>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white shadow rounded p-6">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                <input type="text" name="name" id="name" placeholder="Nama Kategori"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Simpan
            </button>
        </form>
    </div>
@endsection
