<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
    ];


    // Produk milik kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Produk dalam keranjang
    public function cartsItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Produk yang dibeli dalam transaksi
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
