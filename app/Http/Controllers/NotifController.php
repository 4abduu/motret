<?php

namespace App\Http\Controllers;
use App\Models\Notif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function index() {
        $notifications = Notif::where('notify_for', Auth::id())
            ->orderBy('id', 'desc')
            ->get();

            return view('user.notifications', compact('notifications'));
    }

    public function markAsRead($id) {
        $notifications = Notif::findOrFail($id);
        $notifications->status = true;
        $notifications->save();

        return redirect()->back();
    }
}
