@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Daftar Kategori</h2>

        <a href="{{ route('admin.categories.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded mb-4">Tambah Kategori</a>

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-sm">
            <thead>
                <tr>
                    <th class="p-2 text-left">Nama Kategori</th>
                    <th class="p-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td class="p-2">{{ $category->name }}</td>
                        <td class="p-2">
                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                                class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
