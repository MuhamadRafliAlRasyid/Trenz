<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
    @csrf
    @if ($isEdit)
        <!-- Only  if it's editing -->
        @method('PUT')
    @endif

    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

    {{-- Nama Produk --}}
    <div class="mb-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
        <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}"
            placeholder="Contoh: Kaos Polos Hitam"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
    </div>

    {{-- Deskripsi --}}
    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea name="description" id="description" placeholder="Contoh: Kaos berbahan katun yang nyaman dipakai"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">{{ old('description', $product->description ?? '') }}</textarea>
    </div>

    {{-- Kategori --}}
    <div class="mb-4">
        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
        <select name="category_id" id="category_id"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            <option disabled selected>-- Pilih Kategori --</option>
            @foreach ($categories->unique('id') as $category)
                <option value="{{ $category->id }}"
                    {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- SKU --}}
    <div class="mb-4">
        <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku ?? '') }}"
            placeholder="Contoh: SKU12345" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
    </div>

    {{-- Harga --}}
    <div class="mb-4">
        <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
        <input type="number" step="0.01" name="price" id="price"
            value="{{ old('price', $product->price ?? '') }}" placeholder="Contoh: 50000"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
    </div>

    {{-- Stok --}}
    <div class="mb-4">
        <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock ?? '') }}"
            placeholder="Contoh: 100" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
    </div>

    {{-- Satuan --}}
    <div class="mb-4">
        <label for="unit" class="block text-sm font-medium text-gray-700">Satuan</label>
        <input type="text" name="unit" id="unit" value="{{ old('unit', $product->unit ?? 'pcs') }}"
            placeholder="Contoh: pcs, pack, liter"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
    </div>

    {{-- Berat --}}
    <div class="mb-4">
        <label for="weight" class="block text-sm font-medium text-gray-700">Berat (gram)</label>
        <input type="number" step="0.1" name="weight" id="weight"
            value="{{ old('weight', $product->weight ?? 0) }}" placeholder="Contoh: 250"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
    </div>

    {{-- Gambar --}}
    <div class="mb-4">
        <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
        <input type="file" name="image" id="image" class="mt-1 block w-full">
        @if ($isEdit && $product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="Gambar Produk"
                class="mt-2 w-32 h-32 object-cover">
        @endif
    </div>

    {{-- Aktif --}}
    <div class="mb-4">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_active" value="1" class="form-checkbox"
                {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            <span class="ml-2">Aktif</span>
        </label>
    </div>

    {{-- Tombol --}}
    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        {{ $isEdit ? 'Update' : 'Simpan' }}
    </button>
</form>
