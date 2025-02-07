<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use HasFactory;

    protected $table = 'notifs';

    protected $fillable = [
        'notify_for',
        'notify_from',
        'target_id',
        'type',
        'message',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'notify_for', 'target_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'notify_from');
    }

    public function photo()
    {
        return $this->belongsTo(Photo::class,'target_id');
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'target_id');
    }
}