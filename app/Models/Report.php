<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `laporan`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang melaporkan.
 * - `reported_user_id` (int): ID pengguna yang dilaporkan.
 * - `photo_id` (int): ID foto yang dilaporkan (opsional).
 * - `comment_id` (int): ID komentar yang dilaporkan (opsional).
 * - `reply_id` (int): ID balasan yang dilaporkan (opsional).
 * - `reason` (string): Alasan laporan.
 * - `status` (boolean): Status laporan (misalnya, sudah diproses atau belum).
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User` (pelapor).
 * - `reportedUser()`: Relasi many-to-one dengan model `User` (pengguna yang dilaporkan).
 * - `photo()`: Relasi many-to-one dengan model `Photo`.
 * - `comment()`: Relasi many-to-one dengan model `Comment`.
 * - `reply()`: Relasi many-to-one dengan model `Reply`.
 * 
 * Metode:
 * - `getReportCount()`: Menghitung jumlah total laporan.
 */
class Report extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'user_id',
        'reported_user_id',
        'photo_id',
        'comment_id',
        'reply_id',
        'reason',
        'status',
    ];

    /**
     * Relasi many-to-one dengan model `User` (pelapor).
     * Menghubungkan laporan dengan pengguna yang membuat laporan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-one dengan model `User` (pengguna yang dilaporkan).
     * Menghubungkan laporan dengan pengguna yang dilaporkan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user_id');
    }

    /**
     * Relasi many-to-one dengan model `Photo`.
     * Menghubungkan laporan dengan foto yang dilaporkan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    /**
     * Relasi many-to-one dengan model `Comment`.
     * Menghubungkan laporan dengan komentar yang dilaporkan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Relasi many-to-one dengan model `Reply`.
     * Menghubungkan laporan dengan balasan yang dilaporkan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }

    /**
     * Menghitung jumlah total laporan.
     *
     * @return int Jumlah total laporan.
     */
    public static function getReportCount()
    {
        return Report::count();
    }
}