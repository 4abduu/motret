<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'komentar';

    protected $fillable = [
        'comment',
        'user_id',
        'photo_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }

    // Variabel untuk menghitung jumlah komentar
    public static function getCommentCount()
    {
        return self::count();
    }
    
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function reports() 
    {
        return $this->hasMany(Report::class, 'comment_id');
    }
}