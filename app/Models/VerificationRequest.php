<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `permintaan_verifikasi`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang mengajukan permintaan verifikasi.
 * - `full_name` (string): Nama lengkap pengguna.
 * - `username` (string): Nama pengguna.
 * - `reason` (string): Alasan permintaan verifikasi.
 * - `status` (string): Status permintaan verifikasi.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `documents()`: Relasi one-to-many dengan model `VerificationDocument`.
 */
class VerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'username',
        'reason',
        'status',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan permintaan verifikasi dengan pengguna yang mengajukannya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many dengan model `VerificationDocument`.
     * Menghubungkan permintaan verifikasi dengan dokumen-dokumen yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents()
    {
        return $this->hasMany(VerificationDocument::class);
    }

    /**
     * Menghitung jumlah permintaan verifikasi yang ada.
     *
     * @return int
     */
    public static function getVerificationCount()
    {
        return self::count();
    }
}