<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhotoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('foto')->updateOrInsert(
            ['id' => 1],
            [
                'user_id' => 2,
                'title' => 'Anomaliw',
                'description' => 'Sebuah anomali yang terdapat di dunia ini.',
                'path' => 'photos/Tvsh6CsIkiCzehsVBalNDUW9DN5Fvjt09R9fVsyo.jpg',
                'hashtags' => json_encode(['Alomani', 'Windah']),
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
