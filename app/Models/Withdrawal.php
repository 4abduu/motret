<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `tarik_saldo`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang melakukan penarikan.
 * - `amount` (float): Jumlah yang ditarik.
 * - `status` (string): Status penarikan (misalnya, "pending", "completed").
 * - `method` (string): Metode penarikan (misalnya, "bank transfer").
 * - `destination` (string): Tujuan penarikan.
 * - `destination_name` (string): Nama tujuan penarikan.
 * - `note` (string): Catatan tambahan untuk penarikan.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 */
class Withdrawal extends Model
{
    use HasFactory;

    protected $table = 'tarik_saldo';

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'method',
        'destination',
        'destination_name',
        'note',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan penarikan dengan pengguna yang melakukan penarikan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
