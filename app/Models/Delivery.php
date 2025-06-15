<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'courier_id',
        'courier_type',
        'external_service',
        'external_cost',
        'courier_name',
        'courier_service',
        'shipping_cost',
        'tracking_number',
        'status',
        'latitude',
        'longitude',
    ];

    // Relasi ke transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke user kurir
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }
}
