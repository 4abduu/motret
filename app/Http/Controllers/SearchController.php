<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\User;
use App\Models\Search;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $keyword = $request->input('query');
        $search = Search::firstOrCreate(['keyword' => $keyword]);
        $search->increment('count');

        // Cari akun berdasarkan username atau nama, dan hanya pengguna dengan peran 'user'
        $users = User::where('role', 'user')
            ->where(function($q) use ($keyword) {
                $q->where('username', 'LIKE', "%{$keyword}%")
                  ->orWhere('name', 'LIKE', "%{$keyword}%");
            })
            ->get();

        // Ambil ID pengguna yang ditemukan
        $userIds = $users->pluck('id');

        // Cari foto berdasarkan judul, deskripsi, atau hashtag, atau yang diunggah oleh pengguna yang ditemukan
        $photos = Photo::where('banned', false)
            ->where(function($q) use ($keyword, $userIds) {
                $q->whereIn('user_id', $userIds)
                  ->orWhere('title', 'LIKE', "%{$keyword}%")
                  ->orWhere('description', 'LIKE', "%{$keyword}%")
                  ->orWhere('hashtags', 'LIKE', "%{$keyword}%");
            })
            ->get();

        return view('cari.results', compact('users', 'photos', 'keyword'));
    }
}