<?php

namespace App\Http\Controllers;

use App\Models\Notif;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\User; // Tambahkan model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function index()
    {
        $notifications = Notif::where('notify_for', Auth::id())
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($notification) {
                if ($notification->type === 'comment' || $notification->type === 'reply') {
                    return Comment::find($notification->target_id) !== null;
                }
                if ($notification->type === 'like') {
                    return Photo::find($notification->target_id) !== null;
                }
                if ($notification->type === 'follow') {
                    return User::find($notification->target_id) !== null;
                }
                return true;
            });

        return view('user.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notif::findOrFail($id);
        $notification->status = true;
        $notification->save();

        return redirect()->back();
    }
}