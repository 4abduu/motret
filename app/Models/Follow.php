<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `pengikut`.
 * 
 * Atribut:
 * - `follower_id` (int): ID pengguna yang mengikuti.
 * - `following_id` (int): ID pengguna yang diikuti.
 * 
 * Relasi:
 * - `follower()`: Relasi many-to-one dengan model `User` untuk pengguna yang mengikuti.
 * - `following()`: Relasi many-to-one dengan model `User` untuk pengguna yang diikuti.
 */
class Follow extends Model
{
    use HasFactory;

    protected $table = 'pengikut';

    protected $fillable = [
        'follower_id',
        'following_id',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan pengikut dengan pengguna yang mengikuti.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan pengikut dengan pengguna yang diikuti.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
