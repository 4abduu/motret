<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\User;
use App\Models\Search;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{

    /**
     * Menangani pencarian berdasarkan kata kunci dari pengguna.
     *
     * Pencarian dilakukan terhadap:
     * - Akun pengguna (dengan peran 'user' atau 'pro') berdasarkan username atau nama.
     * - Foto berdasarkan judul, deskripsi, hashtag, atau yang diunggah oleh pengguna hasil pencarian akun.
     *
     * Admin tidak diizinkan mengakses halaman pencarian ini.
     * Setiap kata kunci pencarian akan dicatat dan dihitung jumlah pencariannya.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function search(Request $request)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            abort(403, 'Admin tidak diizinkan mengakses halaman ini.');
        }
        Log::info('Metode HTTP:', ['method' => $request->method()]);
        $keyword = $request->input('query');
        $search = Search::firstOrCreate(['keyword' => $keyword]);
        $search->increment('count');

        // Cari akun berdasarkan username atau nama, dan hanya pengguna dengan peran 'user dan pro'
        $users = User::whereIn('role', ['user', 'pro'])
                ->where(function($q) use ($keyword) {
                    $q->where('username', 'LIKE', "%{$keyword}%")
                    ->orWhere('name', 'LIKE', "%{$keyword}%");
                })
                ->get();


        // Ambil ID pengguna yang ditemukan
        $userIds = $users->pluck('id');

        // Cari foto berdasarkan judul, deskripsi, atau hashtag, atau yang diunggah oleh pengguna yang ditemukan
        $photos = Photo::where('banned', false)
            ->where('premium', false)
            ->where('status', true)
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