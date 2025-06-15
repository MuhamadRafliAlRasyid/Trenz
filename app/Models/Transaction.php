<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_type',
        'payment_code',
        'address_id',
        'courier_id',
        'total_price',
        'status',
    ];

    // Relasi dengan model User (Pelanggan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan model Address (Alamat)
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    // Relasi dengan model Courier (Kurir)
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    // Relasi dengan model TransactionDetail (Detail Transaksi)
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetails::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
