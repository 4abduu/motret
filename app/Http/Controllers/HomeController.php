<?php

namespace App\Http\Controllers;

use App\Models\Photo;
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

        return view('home', compact('photos'));
    }
}