<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'foto';

    protected $fillable = [
        'title',
        'description',
        'path',
        'hashtags',
        'likes',
        'status',
        'user_id',
    ];

    /**
     * Get the user that owns the photo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}