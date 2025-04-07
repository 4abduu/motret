<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PHPUnit\Event\Telemetry\System;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            FotoTableSeeder::class,
            SystemSubscriptionPriceSeeder::class,
            SyncBalanceSeeder::class,
        ]);
    }
}
