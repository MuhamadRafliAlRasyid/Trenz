<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartItemController extends Controller
{
    // Tampilkan semua item di cart milik user yang sedang login
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'Unauthorized, user not logged in'], 401);
            }

            $cartItems = CartItem::with(['product.category'])->where('user_id', $userId)->get();

            // Modifikasi path gambar produk agar full URL
            $cartItems->transform(function ($item) {
                if ($item->product && $item->product->image) {
                    $item->product->image = asset('storage/' . $item->product->image);
                }
                return $item;
            });

            return response()->json($cartItems);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Tambah item baru ke cart
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'is_wishlist' => 'sometimes|boolean',
        ]);

        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'Unauthorized, user not logged in'], 401);
        }

        $existing = CartItem::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->quantity += $request->quantity;
            $existing->save();
            return response()->json($existing, 200);
        }

        $cartItem = CartItem::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'is_wishlist' => $request->is_wishlist ?? false,
        ]);


        return response()->json($cartItem, 201);
    }

    // Perbarui jumlah item di cart
    public function update(Request $request, $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($cartItemId);

        // Optional: pastikan item ini milik user yang sedang login
        $userId = Auth::id();
        if ($cartItem->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json($cartItem);
    }

    // Hapus item dari cart
    public function destroy($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);

        // Optional: pastikan item ini milik user yang sedang login
        $userId = Auth::id();
        if ($cartItem->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
    public function wishlist(Request $request)
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $wishlistItems = CartItem::with(['product'])
                ->where('user_id', $userId)
                ->where('is_wishlist', true)
                ->get();

            // Tambahkan URL lengkap ke image
            $wishlistItems->transform(function ($item) {
                if ($item->product && $item->product->image && !str_starts_with($item->product->image, 'http')) {
                    $item->product->image = asset('storage/' . $item->product->image);
                }
                return $item;
            });


            return response()->json($wishlistItems);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // Tambah produk ke wishlist
    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Cek apakah sudah ada di wishlist
        $existing = CartItem::where('user_id', $userId)
            ->where('product_id', $request->product_id)
            ->where('is_wishlist', true)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Produk sudah ada di wishlist'], 200);
        }

        $wishlistItem = CartItem::create([
            'user_id' => $userId,
            'product_id' => $request->product_id,
            'quantity' => 1, // default
            'is_wishlist' => true,
        ]);

        return response()->json(['message' => 'Produk berhasil ditambahkan ke wishlist', 'data' => $wishlistItem], 201);
    }

    public function removeFromWishlist($productId)
    {
        $userId = Auth::id();

        $item = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('is_wishlist', true)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Berhasil dihapus dari wishlist']);
    }
}
