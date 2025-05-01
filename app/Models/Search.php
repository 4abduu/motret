<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel `cari`.
 * 
 * Atribut:
 * - `keyword` (string): Kata kunci pencarian.
 * - `count` (int): Jumlah pencarian untuk kata kunci tersebut.
 * 
 * Relasi:
 * - Tidak ada relasi yang didefinisikan.
 */
class Search extends Model
{
    use HasFactory;
    protected $table = 'cari';
    protected $fillable = [
        'keyword',
        'count',
    ];
}