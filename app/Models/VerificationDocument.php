<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `dokumen_verifikasi`.
 *
 * Atribut:
 * - `verification_request_id` (int): ID permintaan verifikasi yang terkait.
 * - `file_path` (string): Jalur file dokumen verifikasi.
 * - `file_type` (string): Tipe file dokumen verifikasi.
 *
 * Relasi:
 * - `verificationRequest()`: Relasi many-to-one dengan model `VerificationRequest`.
 */
class VerificationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_request_id',
        'file_path',
        'file_type',
    ];

    /**
     * Relasi many-to-one dengan model `VerificationRequest`.
     * Menghubungkan dokumen verifikasi dengan permintaan verifikasi yang terkait.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verificationRequest()
    {
        return $this->belongsTo(VerificationRequest::class);
    }
}