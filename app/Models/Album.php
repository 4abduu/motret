<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `album`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang memiliki album.
 * - `name` (string): Nama album.
 * - `description` (string): Deskripsi album.
 * - `status` (int): Status visibilitas album (misalnya, publik atau privat).
 * 
 * Relasi:
 * - `photos()`: Relasi many-to-many dengan model `Photo`.
 * - `user()`: Relasi one-to-many dengan model `User`.
 */
class Album extends Model
{
    use HasFactory;

    protected $table = 'album';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
    ];

    /**
     * Relasi many-to-many dengan model `Photo`.
     * Menghubungkan album dengan foto-foto yang ada di dalamnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'album_foto', 'album_id', 'photo_id');
    }

    /**
     * Relasi one-to-many dengan model `User`.
     * Menghubungkan album dengan pengguna yang memilikinya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}