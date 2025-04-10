<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Search;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $photos = Photo::where(function ($query) {
            $query->where('banned', false)
                  ->where('premium', false)
                  ->where('status', true)
                  ->orWhere(function ($query) {
                       $query->where('banned', false)
                             ->where('premium', false)
                             ->where('status', true)
                             ->where('updated_at', '>=', now()->subWeek());
                   });
        })->inRandomOrder()
          ->get();

        $mostViewedPhotos = Photo::where('banned', false)->where('premium', false)->where('status', true)->orderBy('views', 'desc')->take(10)->get();
        $mostLikedPhotos = Photo::withCount('likes')->where('banned', false)->where('premium', false)->where('status', true)->orderBy('likes_count', 'desc')->take(10)->get();
        $mostDownloadedPhotos = Photo::withCount('downloads')->where('banned', false)->where('premium', false)->where('status', true)->orderBy('downloads_count', 'desc')->take(10)->get();
        $mostSearchedKeywords = Search::orderBy('count', 'desc')->take(10)->get();

        return view('home', compact('photos', 'mostViewedPhotos', 'mostLikedPhotos', 'mostDownloadedPhotos', 'mostSearchedKeywords'));
    }
}