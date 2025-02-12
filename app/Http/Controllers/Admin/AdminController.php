<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Photo;
use App\Models\Comment;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = User::getUserCount();
        $photoCount = Photo::getPhotoCount();
        $commentCount = Comment::getCommentCount();
        return view('admin.dashboard', compact('userCount', 'photoCount', 'commentCount'));
    }
}