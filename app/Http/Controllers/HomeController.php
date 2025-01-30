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
                  ->orWhere(function ($query) {
                      $query->where('banned', true)
                            ->where('updated_at', '>=', now()->subWeek());
                  });
        })->get();


        $mostViewedPhotos = Photo::where('banned', false)->orderBy('views_today', 'desc')->take(10)->get();
        $mostLikedPhotos = Photo::where('banned', false)->orderBy('likes_today', 'desc')->take(10)->get();
        $mostDownloadedPhotos = Photo::where('banned', false)->orderBy('downloads_today', 'desc')->take(10)->get();
        $mostSearchedKeywords = Search::orderBy('count', 'desc')->take(10)->get();

        return view('home', compact('photos', 'mostViewedPhotos', 'mostLikedPhotos', 'mostDownloadedPhotos', 'mostSearchedKeywords'));
    }
}