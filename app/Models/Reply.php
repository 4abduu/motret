<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `replies`.
 * 
 * Atribut:
 * - `reply` (string): Isi balasan.
 * - `user_id` (int): ID pengguna yang membuat balasan.
 * - `comment_id` (int): ID komentar yang dibalas.
 * 
 * Relasi:
 * - `comment()`: Relasi many-to-one dengan model `Comment`.
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `photo()`: Relasi tidak langsung melalui komentar untuk mendapatkan foto terkait.
 * - `reports()`: Relasi one-to-many dengan model `Report`.
 * 
 * Metode:
 * - `getRepliesCount()`: Menghitung jumlah total balasan.
 */
class Reply extends Model
{
    use HasFactory;

    protected $table = 'replies';

    protected $fillable = ['reply', 'user_id', 'comment_id'];

    /**
     * Relasi many-to-one dengan model `Comment`.
     * Menghubungkan balasan dengan komentar yang dibalas.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan balasan dengan pengguna yang membuatnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi tidak langsung melalui komentar untuk mendapatkan foto terkait.
     * Menghubungkan balasan dengan foto melalui komentar.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function photo(){
        return $this->comment->photo();
    }

    /**
     * Menghitung jumlah total balasan.
     *
     * @return int Jumlah total balasan.
     */
    public static function getRepliesCount(){
        return Reply::count();
    }

    /**
     * Relasi one-to-many dengan model `Report`.
     * Menghubungkan balasan dengan laporan yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports() 
    {
        return $this->hasMany(Report::class, 'reply_id');
    }
}