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
        'likes',
        'status',
        'user_id',
        'banned',
    ];

    protected $casts = [
        'hashtags' => 'array',
    ];

    public function downloads()
    {
        return $this->hasMany(Download::class);
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
        return $this->hasMany(Like::class);
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
}