<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\User;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Cari akun berdasarkan username atau nama, dan hanya pengguna dengan peran 'user'
        $users = User::where('role', 'user')
            ->where(function($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                  ->orWhere('name', 'LIKE', "%{$query}%");
            })
            ->get();

        // Ambil ID pengguna yang ditemukan
        $userIds = $users->pluck('id');

        // Cari foto berdasarkan judul, deskripsi, atau hashtag, atau yang diunggah oleh pengguna yang ditemukan
        $photos = Photo::where('banned', false)
            ->where(function($q) use ($query, $userIds) {
                $q->whereIn('user_id', $userIds)
                  ->orWhere('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('hashtags', 'LIKE', "%{$query}%");
            })
            ->get();

        return view('cari.results', compact('users', 'photos', 'query'));
    }
}