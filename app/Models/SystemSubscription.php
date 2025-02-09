<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSubscription extends Model
{
    use HasFactory;

    protected $table = 'langganan_sistem';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'transaction_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
