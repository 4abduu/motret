<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Photo;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Report;
use App\Models\Reply;
use App\Models\VerificationRequest;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = User::getUserCount();
        $photoCount = Photo::getPhotoCount();
        $verificationCount = VerificationRequest::getVerificationCount();
        $commentCount = Comment::getCommentCount() + Reply::getRepliesCount();
        $reportCount = Report::getReportCount();  
        $transactionCount = Transaction::getTransactionCount();
        return view('admin.dashboard', compact('userCount', 'photoCount', 'commentCount', 'transactionCount', 'verificationCount', 'reportCount'));
    }
}