@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Edit Produk</h2>

        <!-- Pass variables to the form -->
        @include('admin.products.form', [
            'action' => route('admin.products.update', $product->id), // Action set to update with product id
            'product' => $product, // Pass product data to the form
            'categories' => $categories, // Categories for selection
            'isEdit' => true, // Mark as edit
        ])
    </div>
@endsection
