<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    use HasFactory;

    protected $table = 'riwayat_saldo';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'source_id',
        'source_type',
        'status',
        'method',
        'destination',
        'destination_name',
        'note',
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
