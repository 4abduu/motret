<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    public function downloads()
    {
        return $this->hasMany(Download::class, 'photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'photo_id');
    }

    public function isBannedMoreThanAWeek()
    {
        return $this->banned && $this->updated_at->lt(Carbon::now()->subWeek());
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'photo_id');
    }

    public function isLikedBy(?User $user)
    {
        if (!$user) {
            return false;
        }
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Relasi ke notifikasi
    public function notifications()
    {
        return $this->hasMany(Notif::class, 'photo_id');
    }

    public static function getPhotoCount()
    {
        return self::count();
    }

    public function albums()
    {
        return $this->belongsToMany(Album::class, 'album_foto', 'photo_id', 'album_id');
    }

    // Accessor untuk menghitung jumlah likes
    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    // Accessor untuk menghitung jumlah downloads
    public function getDownloadsCountAttribute(): int
    {
        return $this->downloads()->count();
    }
}