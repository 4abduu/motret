<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function profile()
    {
        return view('user.profile');
    }

    public function photos()
    {
        return view('user.photos');
    }
}
