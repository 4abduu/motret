<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'user_id',
        'order_id',
        'transaction_status',
        'payment_type',
        'gross_amount',
        'transaction_id',
        'fraud_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
