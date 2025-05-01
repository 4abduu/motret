<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `langganan_sistem`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang berlangganan.
 * - `price` (float): Harga langganan.
 * - `start_date` (datetime): Tanggal mulai langganan.
 * - `end_date` (datetime): Tanggal berakhir langganan.
 * - `transaction_id` (int): ID transaksi terkait langganan.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `transaction()`: Relasi many-to-one dengan model `Transaction`.
 */
class SubscriptionSystem extends Model
{
    use HasFactory;

    protected $table = 'langganan_sistem';

    protected $fillable = [
        'user_id',
        'price',
        'start_date',
        'end_date',
        'transaction_id',
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan langganan sistem dengan pengguna yang berlangganan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-one dengan model `Transaction`.
     * Menghubungkan langganan sistem dengan transaksi yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Menghitung jumlah total langganan sistem.
     *
     * @return int
     */
    public function getSubscriptionSystemCount()
    {
        return SubscriptionSystem::count();
    }
}
