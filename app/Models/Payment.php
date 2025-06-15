<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'method',
        'status',
        'snap_token',
    ];

    /**
     * Relasi ke tabel transactions.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
