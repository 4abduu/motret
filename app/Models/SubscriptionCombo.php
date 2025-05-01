<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `langganan_kombo`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang melakukan langganan.
 * - `target_user_id` (int): ID kreator atau pengguna yang menjadi target langganan.
 * - `system_price` (float): Harga langganan sistem.
 * - `user_price` (float): Harga langganan pengguna.
 * - `total_price` (float): Total harga langganan (gabungan harga sistem dan pengguna).
 * - `start_date` (datetime): Tanggal mulai langganan.
 * - `end_date` (datetime): Tanggal berakhirnya langganan.
 * - `transaction_id` (int): ID transaksi yang terkait dengan langganan.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User` (pengguna yang berlangganan).
 * - `targetUser()`: Relasi many-to-one dengan model `User` (kreator atau target langganan).
 * - `transaction()`: Relasi many-to-one dengan model `Transaction`.
 * 
 * Metode:
 * - `getSubscriptionComboCount()`: Menghitung jumlah total langganan combo, dengan opsi filter berdasarkan pengguna.
 */
class SubscriptionCombo extends Model
{
    use HasFactory;

    protected $table = 'langganan_kombo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'target_user_id',
        'system_price',
        'user_price',
        'total_price',
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
     * Menghubungkan langganan combo dengan pengguna yang melakukan langganan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan langganan combo dengan kreator atau pengguna yang menjadi target langganan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id', 'id');
    }

    /**
     * Relasi many-to-one dengan model `Transaction`.
     * Menghubungkan langganan combo dengan transaksi yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    /**
     * Menghitung jumlah total langganan combo.
     * 
     * @param int|null $userId ID pengguna (opsional) untuk memfilter langganan berdasarkan pengguna tertentu.
     * @return int Jumlah total langganan combo.
     */
    public static function getSubscriptionComboCount($userId = null)
    {
        return self::when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        })->count();
    }
}
