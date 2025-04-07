<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCombo extends Model
{
    use HasFactory;

    protected $table = 'langganan_kombo';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'target_user_id',
        'system_price',
        'user_price',
        'total_price',
        'start_date',
        'end_date',
        'transaction_id',
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
    ];
    // Relasi ke user yang berlangganan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke kreator/target user
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id', 'id');
    }

    // Relasi ke transaksi
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    // Menghitung jumlah subscription combo (bisa ditambahkan filter jika perlu)
    public static function getSubscriptionComboCount($userId = null)
    {
        return self::when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        })->count();
    }
}
