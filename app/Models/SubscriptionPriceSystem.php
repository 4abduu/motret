<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `harga_langganan_sistem`.
 * 
 * Atribut:
 * - `duration` (int): Durasi langganan dalam bulan.
 * - `price` (float): Harga langganan.
 * 
 * Relasi:
 * - Tidak ada relasi yang didefinisikan.
 */
class SubscriptionPriceSystem extends Model
{
    use HasFactory;

    protected $table = 'harga_langganan_sistem';

    protected $fillable = [
        'duration',
        'price',
    ];

    /**
     * Mengambil semua data harga langganan sistem.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @return
     */
    public function getSubsPriceSystemCount()
    {
        return SubscriptionPriceSystem::count();
    }
}
