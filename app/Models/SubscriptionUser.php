<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUser extends Model
{
    use HasFactory;

    protected $table = 'langganan_user';

    protected $fillable = ['user_id', 'verified_user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedUser()
    {
        return $this->belongsTo(User::class, 'verified_user_id');
    }

    public function getSubscriptionUserCount()
    {
        return SubscriptionUser::count();
    }
}
