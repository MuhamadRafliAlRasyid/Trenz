<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'payment_status',
        'delivery_status',
        'total_price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
