<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `komentar`.
 * 
 * Atribut:
 * - `comment` (string): Isi komentar.
 * - `user_id` (int): ID pengguna yang membuat komentar.
 * - `photo_id` (int): ID foto yang dikomentari.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `photo()`: Relasi many-to-one dengan model `Photo`.
 * - `replies()`: Relasi one-to-many dengan model `Reply`.
 * - `reports()`: Relasi one-to-many dengan model `Report`.
 * 
 * Metode:
 * - `getCommentCount()`: Menghitung jumlah total komentar.
 */
class Comment extends Model
{
    use HasFactory;

    protected $table = 'komentar';

    protected $fillable = [
        'comment',
        'user_id',
        'photo_id',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan komentar dengan pengguna yang membuatnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-one dengan model `Photo`.
     * Menghubungkan komentar dengan foto yang dikomentari.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Menghitung jumlah total komentar.
     *
     * @return int Jumlah komentar.
     */
    public static function getCommentCount()
    {
        return self::count();
    }
    
    /**
     * Relasi one-to-many dengan model `Reply`.
     * Menghubungkan komentar dengan balasan yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * Relasi one-to-many dengan model `Report`.
     * Menghubungkan komentar dengan laporan yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports() 
    {
        return $this->hasMany(Report::class, 'comment_id');
    }
}