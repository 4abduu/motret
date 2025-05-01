<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Model untuk tabel `foto`.
 * 
 * Atribut:
 * - `title` (string): Judul foto.
 * - `description` (string): Deskripsi foto.
 * - `path` (string): Path atau URL foto.
 * - `hashtags` (array): Hashtags terkait foto.
 * - `status` (string): Status foto (misalnya, aktif, tidak aktif).
 * - `premium` (boolean): Menandakan apakah foto premium atau tidak.
 * - `user_id` (int): ID pengguna yang mengunggah foto.
 * - `banned` (boolean): Menandakan apakah foto dibanned atau tidak.
 * - `views` (int): Jumlah tampilan foto.
 * - `created_at` (datetime): Tanggal pembuatan foto.
 * - `updated_at` (datetime): Tanggal terakhir pembaruan foto.
 * 
 * Relasi:
 * - `downloads()`: Relasi one-to-many dengan model `Download`.
 * - `user()`: Relasi many-to-one dengan model `User`.
 * - `reports()`: Relasi one-to-many dengan model `Report`.
 * - `likes()`: Relasi one-to-many dengan model `Like`.
 * - `comments()`: Relasi one-to-many dengan model `Comment`.
 * - `notifications()`: Relasi one-to-many dengan model `Notif`.
 * - `albums()`: Relasi many-to-many dengan model `Album`.
 * 
 * Metode:
 * - `getPhotoCount()`: Menghitung jumlah total foto.
 * - `isBannedMoreThanAWeek()`: Memeriksa apakah foto telah dibanned lebih dari satu minggu.
 * - `isLikedBy()`: Memeriksa apakah foto disukai oleh pengguna tertentu.
 * - `getLikesCountAttribute`: Menghitung jumlah suka pada foto.
 * - `getDownloadsCountAttribute`: Menghitung jumlah unduhan pada foto.
 */
class Photo extends Model
{
    use HasFactory;

    protected $table = 'foto';

    protected $fillable = [
        'title',
        'description',
        'path',
        'hashtags',
        'status',
        'premium',
        'user_id',
        'banned',
        'views',
    ];

    protected $casts = [
        'hashtags' => 'array',
    ];

    /**
     * Relasi one-to-many dengan model `Download`.
     * Menghubungkan foto dengan unduhan yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloads()
    {
        return $this->hasMany(Download::class, 'photo_id');
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan foto dengan pengguna yang mengunggahnya.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi one-to-many dengan model `Report`.
     * Menghubungkan foto dengan laporan yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'photo_id');
    }

    /**
     * Memeriksa apakah user telah dibanned lebih dari satu minggu.
     *
     * @return bool True kalau user dibanned lebih dari seminggu, false kalau belum.
     */
    public function isBannedMoreThanAWeek()
    {
        return $this->banned && $this->updated_at->lt(Carbon::now()->subWeek());
    }

    /**
     * Relasi one-to-many dengan model `Like`.
     * Menghubungkan foto dengan suka yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'photo_id');
    }

    /**
     * Memeriksa apakah foto disukai oleh pengguna tertentu.
     *
     * @param \App\Models\User|null $user Pengguna yang akan diperiksa.
     * @return bool True jika disukai, false jika tidak.
     */
    public function isLikedBy(?User $user)
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Relasi one-to-many dengan model `Comment`.
     * Menghubungkan foto dengan komentar yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relasi one-to-many dengan model `Notif`.
     * Menghubungkan foto dengan notifikasi yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notif::class, 'photo_id');
    }

    /**
     * Menghitung jumlah total foto.
     *
     * @return int Jumlah foto.
     */
    public static function getPhotoCount()
    {
        return self::count();
    }

    /**
     * Relasi many-to-many dengan model `Album`.
     * Menghubungkan foto dengan album yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_foto', 'photo_id', 'album_id');
    }

    /**
     * Menghitung jumlah suka pada foto.
     *
     * @return int Jumlah suka.
     */
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    /**
     * Menghitung jumlah unduhan pada foto.
     *
     * @return int Jumlah unduhan.
     */
    public function getDownloadsCountAttribute(): int
    {
        return $this->downloads()->count();
    }
}