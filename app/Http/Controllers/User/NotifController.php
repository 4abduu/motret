<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notif;
use App\Models\Comment;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Notif::where('notify_for', Auth::id())
            ->orderBy('id', 'desc')
            ->get();

        // Log data notifikasi sebelum filter diterapkan
        Log::info('Notifications before filter:', $notifications->toArray());

        $notifications = $notifications->filter(function ($notification) {
            if ($notification->type === 'comment' || $notification->type === 'reply') {
                return Photo::find($notification->target_id) !== null;
            }
            if ($notification->type === 'like') {
                return Photo::find($notification->target_id) !== null;
            }
            if ($notification->type === 'follow') {
                return User::find($notification->target_id) !== null;
            }
            return true;
        });

        // Log data notifikasi setelah filter diterapkan
        Log::info('Notifications after filter:', $notifications->toArray());

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