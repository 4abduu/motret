<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'langganan_user';

    protected $fillable = [
        'user_id',
        'target_user_id',
        'price',
        'start_date',
        'end_date',
        'transaction_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
