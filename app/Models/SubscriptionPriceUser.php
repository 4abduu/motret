<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `harga_langganan_pengguna`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang memiliki harga langganan.
 * - `price_1_month` (float): Harga langganan untuk 1 bulan.
 * - `price_3_months` (float): Harga langganan untuk 3 bulan.
 * - `price_6_months` (float): Harga langganan untuk 6 bulan.
 * - `price_1_year` (float): Harga langganan untuk 1 tahun.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 */
class SubscriptionPriceUser extends Model
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
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan harga langganan pengguna dengan pengguna yang memiliki harga tersebut.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menghitung jumlah total pengguna yang memiliki harga langganan.
     *
     * @return int
     */
    public function getSubsPriceUserCount()
    {
        return SubscriptionPriceUser::count();
    }
}
