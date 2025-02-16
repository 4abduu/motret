<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPriceSystem extends Model
{
    use HasFactory;

    protected $table = 'harga_langganan_sistem';

    protected $fillable = [
        'duration',
        'price',
    ];

    public function getSubsPriceSystemCount()
    {
        return SubscriptionPriceSystem::count();
    }
}
