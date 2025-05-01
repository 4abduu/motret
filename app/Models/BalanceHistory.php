<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `riwayat_saldo`.
 * 
 * Atribut:
 * - `user_id` (int): ID pengguna yang terkait dengan riwayat saldo.
 * - `type` (string): Jenis riwayat saldo (misalnya, "income" atau "withdrawal").
 * - `amount` (float): Jumlah saldo yang terlibat dalam riwayat ini.
 * - `source_id` (int): ID sumber transaksi (misalnya, ID penarikan atau langganan).
 * - `source_type` (string): Jenis sumber transaksi (misalnya, "withdrawal" atau "subscription").
 * - `status` (string): Status riwayat saldo (misalnya, "pending", "success", atau "rejected").
 * - `method` (string): Metode transaksi (misalnya, "bank transfer" atau "e-wallet").
 * - `destination` (string): Tujuan transaksi (misalnya, nomor rekening atau e-wallet).
 * - `destination_name` (string): Nama pemilik tujuan transaksi.
 * - `note` (string): Catatan tambahan terkait riwayat saldo.
 * 
 * Relasi:
 * - `user()`: Relasi many-to-one dengan model `User`.
 */
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

    /**
     * Relasi many-to-one dengan model `User`.
     * Menghubungkan riwayat saldo dengan pengguna yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
