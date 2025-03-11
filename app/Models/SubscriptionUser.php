<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUser extends Model
{
    use HasFactory;

    protected $table = 'langganan_pengguna';

    protected $fillable = ['user_id', 'target_user_id', 'price', 'start_date', 'end_date', 'transaction_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function getSubscriptionUserCount()
    {
        return SubscriptionUser::count();
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}