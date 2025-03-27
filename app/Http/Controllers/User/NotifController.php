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
        // Get base notifications query with eager loading
        $query = Notif::with(['sender', 'photo', 'comment'])
            ->where('notify_for', Auth::id())
            ->orderBy('created_at', 'desc');
    
        // Apply filters directly in the query for better performance
        $query->where(function($q) {
            $q->where(function($sub) {
                // Notifications that require photo to exist
                $sub->whereIn('type', ['comment', 'reply', 'like'])
                    ->whereHas('photo', function($photoQuery) {
                        $photoQuery->whereNotNull('id');
                    });
            })->orWhere(function($sub) {
                // Follow notifications that require user to exist
                $sub->where('type', 'follow')
                    ->whereHas('sender', function($userQuery) {
                        $userQuery->whereNotNull('id');
                    });
            })->orWhere('type', 'system')
              ->orWhere(function($sub) {
                  // Comment notifications
                  $sub->whereIn('type', ['comment', 'reply'])
                      ->whereHas('comment', function($commentQuery) {
                          $commentQuery->whereNotNull('id');
                      });
              });
        });
    
        // Paginate the results (15 items per page by default)
        $notifications = $query->paginate(15);
    
        // Transform the collection to include proper target data
        $notifications->getCollection()->transform(function($notification) {
            // For follow notifications, set the target user
            if ($notification->type === 'follow') {
                $notification->target = User::find($notification->target_id);
            }
            return $notification;
        });
    
        // Log the paginated results
        Log::info('Paginated notifications:', [
            'total' => $notifications->total(),
            'current_page' => $notifications->currentPage(),
            'data_sample' => $notifications->items()[0] ?? null
        ]);
    
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