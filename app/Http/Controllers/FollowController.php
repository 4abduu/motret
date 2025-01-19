<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow($userId)
    {
        $user = User::findOrFail($userId);
        $authUser = Auth::user();

        if (!$authUser->isFollowing($user)) {
            Follow::create([
                'follower_id' => $authUser->id,
                'following_id' => $user->id,
            ]);
        }

        return response()->json([
            'following' => true,
            'followers_count' => $user->followers()->count(),
            'following_count' => $authUser->following()->count(),
            'username' => $user->username,
        ]);
    }

    public function unfollow($userId)
    {
        $user = User::findOrFail($userId);
        $authUser = Auth::user();

        $follow = Follow::where('follower_id', $authUser->id)->where('following_id', $user->id)->first();
        if ($follow) {
            $follow->delete();
        }

        return response()->json([
            'following' => false,
            'followers_count' => $user->followers()->count(),
            'following_count' => $authUser->following()->count(),
            'username' => $user->username,
        ]);
    }
}