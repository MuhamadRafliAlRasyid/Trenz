<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'verification_code',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Transaksi yang dilakukan oleh user
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Alamat pengiriman user
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Keranjang belanja user
    public function cartUtems()
    {
        return $this->hasMany(CartItem::class);
    }
}
