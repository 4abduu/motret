<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $table = 'album';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cover_photo',
        'status',
    ];

    public function photos()
    {
        return $this->belongsToMany(Photo::class, 'album_foto', 'album_id', 'photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}