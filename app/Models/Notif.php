<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `notifs`.
 * 
 * Atribut:
 * - `notify_for` (int): ID pengguna yang menerima notifikasi.
 * - `notify_from` (int): ID pengguna yang mengirim notifikasi.
 * - `target_id` (int): ID target notifikasi (misalnya, ID foto atau komentar).
 * - `type` (string): Jenis notifikasi (misalnya, 'like', 'comment', dll.).
 * - `message` (string): Pesan notifikasi.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `sender()`: Relasi many-to-one dengan model `User`.
 * - `photo()`: Relasi many-to-one dengan model `Photo`.
 * - `comment()`: Relasi many-to-one dengan model `Comment`.
 */
class Notif extends Model
{
    use HasFactory;

    protected $table = 'notifs';

    protected $fillable = [
        'notify_for',
        'notify_from',
        'target_id',
        'type',
        'message',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan notifikasi dengan pengguna yang menerimanya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'notify_for', 'target_id');
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan notifikasi dengan pengguna yang mengirimnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'notify_from');
    }

    /**
     * Relasi many-to-one dengan model `Photo`.
     * Menghubungkan notifikasi dengan foto yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class,'target_id');
    }

    /**
     * Relasi many-to-one dengan model `Comment`.
     * Menghubungkan notifikasi dengan komentar yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'target_id');
    }
}