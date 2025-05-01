<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `transaksi`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang melakukan transaksi.
 * - `order_id` (string): ID pesanan.
 * - `target_user_id` (int): ID pengguna yang menjadi target transaksi.
 * - `transaction_status` (string): Status transaksi.
 * - `payment_type` (string): Jenis pembayaran.
 * - `gross_amount` (float): Jumlah total transaksi.
 * - `transaction_id` (string): ID transaksi.
 * - `fraud_status` (string): Status penipuan.
 * - `type` (string): Tipe transaksi.
 * - `metadata` (array): Metadata tambahan untuk transaksi.
 */
class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'user_id',
        'order_id',
        'target_user_id',
        'transaction_status',
        'payment_type',
        'gross_amount',
        'transaction_id',
        'fraud_status',
        'type',
        'metadata',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan transaksi dengan pengguna yang melakukan transaksi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menghitung jumlah total transaksi pengguna.
     *
     * @return int
     */
    public static function getTransactionCount()
    {
        return Transaction::count();
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan transaksi dengan pengguna yang menjadi target transaksi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}