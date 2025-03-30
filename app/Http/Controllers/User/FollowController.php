<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Follow;
use App\Models\Notif; // Pastikan model Notif diimport
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function follow($userId)
    {
        $user = User::findOrFail($userId);
        $authUser = Auth::user();
    
        if ($authUser->isFollowing($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Already following this user'
            ]);
        }
    
        Follow::create([
            'follower_id' => $authUser->id,
            'following_id' => $user->id,
        ]);
    
        // Buat notifikasi
        Notif::create([
            'notify_for' => $user->id,
            'notify_from' => $authUser->id,
            'target_id' => $user->id,
            'type' => 'follow',
            'message' => 'started following you.',
        ]);
    
        return response()->json([
            'success' => true,
            'action' => 'follow',
            'followers_count' => $user->followers()->count(),
            'following_count' => $authUser->following()->count(),
            'current_user' => [
                'id' => $authUser->id,
                'username' => $authUser->username
            ],
            'target_user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);
    }
    
    public function unfollow($userId)
    {
        $user = User::findOrFail($userId);
        $authUser = Auth::user();
    
        $follow = Follow::where('follower_id', $authUser->id)
                       ->where('following_id', $user->id)
                       ->first();
    
        if (!$follow) {
            return response()->json([
                'success' => false,
                'message' => 'Not following this user'
            ]);
        }
    
        $follow->delete();
    
        // Hapus notifikasi
        Notif::where('notify_from', $authUser->id)
             ->where('type', 'follow')
             ->where('notify_for', $user->id)
             ->delete();
    
        return response()->json([
            'success' => true,
            'action' => 'unfollow',
            'followers_count' => $user->followers()->count(),
            'following_count' => $authUser->following()->count(),
            'current_user' => [
                'id' => $authUser->id,
                'username' => $authUser->username
            ],
            'target_user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
        ]);
    }
}