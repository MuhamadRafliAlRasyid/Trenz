@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Tambah Produk</h2>

        <!-- Pass variables to the form -->
        @include('admin.products.form', [
            'action' => route('admin.products.store'), // Action set to store
            'product' => null, // No product data for creating
            'categories' => $categories, // Categories for selection
            'isEdit' => false, // Not editing, so false
        ])
    </div>
@endsection
