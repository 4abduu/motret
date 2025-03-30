<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'replies';

    protected $fillable = ['reply', 'user_id', 'comment_id'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function photo(){
        return $this->comment->photo();
    }

    public static function getRepliesCount(){
        return Reply::count();
    }

    public function reports() 
    {
        return $this->hasMany(Report::class, 'reply_id');
    }
}