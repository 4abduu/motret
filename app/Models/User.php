<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_photo',
        'role',
        'subscription_ends_at',
        'status',
        'download_reset_at',
        'google_id',
        'verified',
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

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }
    
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/photo_profile/' . $this->profile_photo);
        }

        return asset('storage/photo_profile/default_photo_profile.jpg');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'pengikut', 'following_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'pengikut', 'follower_id', 'following_id');
    }

    public function isFollowing(User $user)
    {
        return $this->following()->where('users.id', $user->id)->exists();
    }

    public function follow(User $user)
    {
        return $this->following()->attach($user->id);
    }

    public function unfollow(User $user)
    {
        return $this->following()->detach($user->id);
    }

    public function hasLiked(Photo $photo)
    {
        return $this->likes()->where('photo_id', $photo->id)->exists();
    }

    public function like(Photo $photo)
    {
        return $this->likes()->create(['photo_id' => $photo->id]);
    }

    public function unlike(Photo $photo)
    {
        return $this->likes()->where('photo_id', $photo->id)->delete();
    }

    // Relasi ke notifikasi yang diterima
    public function notifications()
    {
        return $this->hasMany(Notif::class, 'notify_for');
    }

    // Relasi ke notifikasi yang dikirim
    public function sentNotifications()
    {
        return $this->hasMany(Notif::class, 'notify_from');
    }

    // Variabel untuk menghitung jumlah pengguna
    public static function getUserCount()
    {
        return self::count();
    }
    // User yang berlangganan ke user lain
    public function subscriptions()
    {
        return $this->hasMany(SubscriptionUser::class);
    }

    // User verif yang memiliki pelanggan
    public function subscribers()
    {
        return $this->hasMany(SubscriptionUser::class, 'verified_user_id');
    }

}