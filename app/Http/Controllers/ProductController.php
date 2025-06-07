<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Menampilkan daftar produk dengan filter kategori
    public function index(Request $request)
    {
        $categories = Category::all(); // Ambil semua kategori
        $query = Product::query();

        // Filter berdasarkan kategori
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get(); // Ambil produk sesuai dengan filter kategori

        return view('admin.products.index', compact('products', 'categories'));
    }
    // API endpoint untuk mobile frontend
    public function apiIndex()
    {
        $products = Product::with('category')->where('is_active', true)->get();

        // Ubah path gambar menjadi URL publik
        $products->transform(function ($product) {
            $product->image = $product->image
                ? asset('storage/' . $product->image)
                : null;
            return $product;
        });

        return response()->json($products);
    }


    // Menampilkan form create produk
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // Menampilkan form edit produk
    public function edit($id)
    {
        $product = Product::findOrFail($id); // Cari produk berdasarkan ID
        $categories = Category::all(); // Ambil semua kategori
        return view('admin.products.edit', compact('product', 'categories')); // Tampilkan form edit produk
    }

    // Menyimpan produk baru
    public function store(Request $request)
    {
        // Validasi input produk
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Ambil data input kecuali image
        $data = $request->except('image');

        // Cek jika ada gambar baru
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '-' . $file->getClientOriginalName();
            $path = $file->storeAs('products', $filename, 'public');
            $data['image'] = $path;
        }

        // Simpan produk baru ke database
        $product = Product::create($data);

        // Redirect ke halaman produk dengan pesan sukses
        return redirect()->route('admin.products.index')->with('status', 'Product created successfully');
    }

    // Memperbarui produk yang ada
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id); // Cari produk berdasarkan ID

        // Validasi data input
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $id,
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'weight' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Jika validasi gagal, kirimkan error
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        // Ambil data kecuali image
        $data = $request->except('image');

        // Handle image update
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
            }

            // Simpan gambar baru
            $file = $request->file('image');
            $filename = time() . '-' . $file->getClientOriginalName();
            $path = $file->storeAs('products', $filename, 'public');
            $data['image'] = $path; // Simpan path gambar baru ke database
        }

        // Update produk
        $product->update($data);

        // Redirect ke halaman produk
        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully');
    }
    // Menghapus produk
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        // Hapus gambar produk jika ada
        if ($product->image && Storage::exists('public/' . $product->image)) {
            Storage::delete('public/' . $product->image);
        }

        // Hapus produk
        $product->delete();

        // Redirect ke halaman produk
        return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully');
    }
}
