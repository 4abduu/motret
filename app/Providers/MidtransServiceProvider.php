<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MidtransServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    /**
     * Melakukan bootstrap layanan Midtrans dengan mengatur konfigurasi yang dibutuhkan.
     *
     * Method ini menginisialisasi konfigurasi untuk Midtrans dengan menggunakan nilai yang ada 
     * pada file konfigurasi `midtrans.php`. Ini memastikan aplikasi menggunakan kunci server yang benar 
     * serta pengaturan untuk mode produksi, sanitasi data, dan 3D Secure.
     */
    public function boot()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
    }
}