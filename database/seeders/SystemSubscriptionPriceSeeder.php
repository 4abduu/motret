<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemSubscriptionPriceSeeder extends Seeder
{
    public function run()
    {
        DB::table('harga_langganan_sistem')->insert([
            [
                'duration' => '1_month',
                'price' => 25000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'duration' => '3_months',
                'price' => 70000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'duration' => '6_months',
                'price' => 135000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'duration' => '1_year',
                'price' => 275000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
