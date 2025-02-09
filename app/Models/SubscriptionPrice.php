<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPrice extends Model
{
    use HasFactory;

    protected $table = 'harga_langganan_pengguna';

    protected $fillable = [
        'user_id',
        'price_1_month',
        'price_3_months',
        'price_6_months',
        'price_1_year',
    ];

    /**
     * Relasi ke User (Pemilik harga langganan).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
