<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `download`.
 * 
 * Atribut:
 * - `photo_id` (int): ID foto yang diunduh.
 * - `user_id` (int): ID pengguna yang mengunduh foto.
 * - `guest_id` (int): ID tamu yang mengunduh foto.
 * - `count_downloads` (int): Jumlah unduhan foto.
 * - `resolution` (string): Resolusi foto yang diunduh.
 * 
 * Relasi:
 * - `photo()`: Relasi many-to-one dengan model `Photo`.
 * - `user()`: Relasi many-to-one dengan model `User`.
 */
class Download extends Model
{
    use HasFactory;

    protected $fillable =  ['photo_id', 
                            'user_id',
                            'guest_id',
                            'count_downloads', 
                            'resolution',];

    /**
     * Relasi many-to-one dengan model `Photo`.
     * Menghubungkan unduhan dengan foto yang diunduh.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */                        
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan unduhan dengan pengguna yang mengunduhnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
