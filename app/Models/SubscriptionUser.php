<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class SubscriptionUser extends Model
{
    use HasFactory;

    protected $table = 'langganan_pengguna';

    protected $fillable = ['user_id', 'target_user_id', 'price', 'start_date', 'end_date', 'transaction_id'];

    protected $casts = [
        'end_date' => 'datetime',
        'start_date' => 'datetime',
    ];

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan langganan pengguna dengan pengguna yang berlangganan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan langganan pengguna dengan kreator atau pengguna yang menjadi target langganan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Menghitung jumlah total langganan pengguna.
     *
     * @return int
     */
    public function getSubscriptionUserCount()
    {
        return SubscriptionUser::count();
    }

    /**
     * Relasi many-to-one dengan model `Transaction`.
     * Menghubungkan langganan pengguna dengan transaksi yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}