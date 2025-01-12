<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Photo;

class PhotoSeeder extends Seeder
{
    public function run()
    {
        // Ambil data dari tabel 'photos'
        $photos = DB::table('photos')->get();

        // Isi data ke dalam tabel 'photos' menggunakan model Photo
        foreach ($photos as $photo) {
            Photo::create([
                'user_id' => $photo->user_id,
                'title' => $photo->title,
                'description' => $photo->description,
                'path' => $photo->path,
                'hashtags' => $photo->hashtags,
                'status' => $photo->status,
            ]);
        }
    }
}
