<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'verification_request_id',
        'file_path',
        'file_type',
    ];

    public function verificationRequest()
    {
        return $this->belongsTo(VerificationRequest::class);
    }
}