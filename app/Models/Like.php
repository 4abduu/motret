<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `suka`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang menyukai foto.
 * - `photo_id` (int): ID foto yang disukai.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `photo()`: Relasi many-to-one dengan model `Photo`.
 */
class Like extends Model
{
    use HasFactory;

    protected $table = 'suka';

    protected $fillable = [
        'user_id',
        'photo_id',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan suka dengan pengguna yang menyukainya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-one dengan model `Photo`.
     * Menghubungkan suka dengan foto yang disukai.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

}
