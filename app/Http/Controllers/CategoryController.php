<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan semua kategori
    public function index()
    {
        $categories = Category::all(); // Mengambil semua kategori
        return view('admin.categories.index', compact('categories'));
    }

    // Menampilkan form untuk menambah kategori
    public function create()
    {
        return view('admin.categories.create');
    }

    // Menyimpan kategori baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create($request->all()); // Menyimpan kategori

        return redirect()->route('admin.categories.index')->with('status', 'Category created successfully');
    }

    // Menampilkan form untuk mengedit kategori
    public function edit($id)
    {
        $category = Category::findOrFail($id); // Mencari kategori berdasarkan ID
        return view('admin.categories.edit', compact('category'));
    }

    // Memperbarui kategori
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id); // Mencari kategori
        $category->update($request->all()); // Memperbarui kategori

        return redirect()->route('admin.categories.index')->with('status', 'Category updated successfully');
    }

    // Menghapus kategori
    public function destroy($id)
    {
        $category = Category::findOrFail($id); // Mencari kategori
        $category->delete(); // Menghapus kategori

        return redirect()->route('admin.categories.index')->with('status', 'Category deleted successfully');
    }
}
