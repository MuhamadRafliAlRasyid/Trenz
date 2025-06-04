<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get([
            'id',
            'name',
            'price',
            'image',
            'category_id',
            'description'
        ]);

        return view('admin.products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::where('id', $id)->where('is_active', true)->first();

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $product]);
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $action = route('admin.products.update', $product->id); // Pastikan ini ada
        $isEdit = true;
        return view('admin.products.form', compact('product', 'action', 'isEdit'));
    }


    public function store(Request $request)
    {
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
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '-' . $file->getClientOriginalName();
            $path = $file->storeAs('products', $filename, 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        // Redirect ke halaman produk
        return redirect()->route('admin.products.index')->with('status', 'Product created successfully');
    }



    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

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

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($product->image && Storage::exists('public/' . $product->image)) {
                Storage::delete('public/' . $product->image);
            }

            $filename = uniqid() . '.' . $request->image->extension();
            $request->file('image')->storeAs('public/products', $filename);
            $data['image'] = 'products/' . $filename;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully');
    }



    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['status' => false, 'message' => 'Product not found'], 404);
        }

        if ($product->image && Storage::exists('public/' . $product->image)) {
            Storage::delete('public/' . $product->image);
        }

        $product->delete();

        // Redirect ke halaman produk
        return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully');
    }
}
