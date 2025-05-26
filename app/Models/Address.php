<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Transaction;

class Address extends Model
{
    // Kolom yang boleh diisi secara massal (mass assignment)
    protected $fillable = [
        'user_id',
        'receiver_name',
        'phone',
        'province',
        'city',
        'district',
        'postal_code',
        'detail',
    ];

    // Relasi ke User (sebuah alamat dimiliki oleh satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Transaction (satu alamat bisa punya banyak transaksi)
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
