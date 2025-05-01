<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

/**
 * Model untuk tabel `users`.
 * 
 * Atribut:
 * - `name` (string): Nama pengguna.
 * - `username` (string): Username pengguna.
 * - `email` (string): Alamat email pengguna.
 * - `bio` (string): Biografi pengguna.
 * - `website` (string): URL situs web pengguna.
 * - `password` (string): Kata sandi pengguna (hashed).
 * - `profile_photo` (string): Nama file foto profil pengguna.
 * - `role` (string): Peran pengguna (misalnya, admin, pro, user).
 * - `subscription_ends_at` (datetime): Tanggal berakhirnya langganan pengguna.
 * - `status` (int): Status pengguna.
 * - `download_reset_at` (datetime): Tanggal reset unduhan pengguna.
 * - `google_id` (string): ID Google untuk login sosial.
 * - `verified` (boolean): Status verifikasi pengguna.
 * - `balance` (float): Saldo pengguna.
 * - `banned_type` (string): Jenis banned (temporary atau permanent).
 * - `banned_until` (datetime): Tanggal berakhirnya banned.
 * - `banned_reason` (string): Alasan banned.
 * 
 * Relasi:
 * - `downloads()`: Relasi one-to-many dengan model `Download`.
 * - `photos()`: Relasi one-to-many dengan model `Photo`.
 * - `albums()`: Relasi one-to-many dengan model `Album`.
 * - `likes()`: Relasi one-to-many dengan model `Like`.
 * - `comments()`: Relasi one-to-many dengan model `Comment`.
 * - `followers()`: Relasi many-to-many dengan model `User` (pengikut).
 * - `following()`: Relasi many-to-many dengan model `User` (mengikuti).
 * - `subscriptions()`: Relasi one-to-many dengan model `SubscriptionUser`.
 * - `subscribers()`: Relasi one-to-many dengan model `SubscriptionUser` (sebagai target).
 * - `subscriptionPrice()`: Relasi one-to-one dengan model `SubscriptionPriceUser`.
 * - `withdrawals()`: Relasi one-to-many dengan model `Withdrawal`.
 * - `balanceHistory()`: Relasi one-to-many dengan model `BalanceHistory`.
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'bio',
        'website',
        'password',
        'profile_photo',
        'role',
        'subscription_ends_at',
        'status',
        'download_reset_at',
        'google_id',
        'verified',
        'balance',
        'banned_type',
        'banned_until',
        'banned_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'subscription_ends_at' => 'datetime',
        'download_reset_at' => 'datetime',
        'verified' => 'boolean',
        'banned_until' => 'datetime',
    ];

    /**
     * Relasi one-to-many dengan model `Download`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

    /**
     * Mengecek apakah user tersebut memiliki foto profil atau tidak.
     * Jika tidak ada, maka akan mengembalikan foto profil default.
     * 
     * @return string
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @return 
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/photo_profile/' . $this->profile_photo);
        }

        return asset('storage/photo_profile/default_photo_profile.jpg');
    }

    /**
     * Relasi one-to-many dengan model `Photo`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    /**
     * Relasi one-to-many dengan model `Album`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function albums()
    {
        return $this->hasMany(Album::class);
    }

    /**
     * Relasi one-to-many dengan model `Like`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Relasi one-to-many dengan model `Comment`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Relasi many-to-many dengan model `User` (pengikut).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'pengikut', 'following_id', 'follower_id');
    }

    /**
     * Relasi many-to-many dengan model `User` (mengikuti).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'pengikut', 'follower_id', 'following_id');
    }

    /**
     * Memeriksa apakah pengguna saat ini mengikuti pengguna lain.
     *
     * @param \App\Models\User $user Pengguna target.
     * @return bool True jika mengikuti, false jika tidak.
     */
    public function isFollowing(User $user)
    {
        return $this->following()->where('users.id', $user->id)->exists();
    }

    /**
     * Menambahkan pengguna lain ke daftar yang diikuti.
     *
     * @param \App\Models\User $user Pengguna target.
     * @return void
     */
    public function follow(User $user)
    {
        return $this->following()->attach($user->id);
    }

    /**
     * Menghapus pengguna lain dari daftar yang diikuti.
     *
     * @param \App\Models\User $user Pengguna target.
     * @return void
     */
    public function unfollow(User $user)
    {
        return $this->following()->detach($user->id);
    }

    /**
     * Memeriksa apakah pengguna saat ini menyukai foto tertentu.
     *
     * @param \App\Models\Photo $photo Foto yang akan diperiksa.
     * @return bool True jika menyukai, false jika tidak.
     */
    public function hasLiked(Photo $photo)
    {
        return $this->likes()->where('photo_id', $photo->id)->exists();
    }

    /**
     * Menyukai foto.
     *
     * @param \App\Models\Photo $photo Foto yang akan disukai.
     * @return \App\Models\Like
     */
    public function like(Photo $photo)
    {
        return $this->likes()->create(['photo_id' => $photo->id]);
    }

    /**
     * Menghapus suka pada foto.
     *
     * @param \App\Models\Photo $photo Foto yang akan dihapus suka-nya.
     * @return int Jumlah baris yang terpengaruh.
     */
    public function unlike(Photo $photo)
    {
        return $this->likes()->where('photo_id', $photo->id)->delete();
    }

    /**
     * Relasi one-to-many dengan model `Notif`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notif::class, 'notify_for');
    }

    /**
     * Relasi one-to-many dengan model `Notif` (sebagai pengirim).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notif::class, 'notify_from');
    }

    /**
     * Menghitung jumlah pengguna yang terdaftar.
     *
     * @return int Jumlah pengguna.
     */
    public static function getUserCount()
    {
        return self::count();
    }

    /**
     * Relasi one-to-many dengan model `SubscriptionUser`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(SubscriptionUser::class);
    }

    /**
     * Relasi one-to-many dengan model `SubscriptionUser` (sebagai target).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribers()
    {
        return $this->hasMany(SubscriptionUser::class, 'target_user_id');
    }

    /**
     * Relasi one-to-one dengan model `SubscriptionPriceUser`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscriptionPrice()
    {
        return $this->hasOne(SubscriptionPriceUser::class, 'user_id');
    }

    /**
     * Relasi one-to-many dengan model `Withdrawal`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Relasi one-to-many dengan model `BalanceHistory`.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function balanceHistory()
    {
        return $this->hasMany(BalanceHistory::class);
    }

    /**
     * Memeriksa apakah pengguna saat ini berlangganan ke pengguna lain.
     *
     * @param \App\Models\User $user Pengguna target.
     * @return bool True jika berlangganan, false jika tidak.
     */
    public function isSubscribedTo(User $user)
    {
        return $this->subscriptions()->where('target_user_id', $user->id)->exists();
    }

    /**
     * Menyinkronkan saldo pengguna berdasarkan total harga langganan yang diterima.
     *
     * @return void
     */
    public function syncBalance()
    {
        $total = DB::table('langganan_pengguna')
            ->where('target_user_id', $this->id)
            ->sum('price');

        $this->balance = $total;
        $this->save();
    }
}