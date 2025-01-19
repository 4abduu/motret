<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like($photoId)
    {
        $photo = Photo::findOrFail($photoId);
        $user = Auth::user();

        if (!$photo->isLikedBy($user)) {
            Like::create([
                'user_id' => $user->id,
                'photo_id' => $photo->id,
            ]);
        }

        return response()->json([
            'liked' => true,
            'likes_count' => $photo->likes()->count(),
        ]);
    }

    public function unlike($photoId)
    {
        $photo = Photo::findOrFail($photoId);
        $user = Auth::user();

        $like = Like::where('user_id', $user->id)->where('photo_id', $photo->id)->first();
        if ($like) {
            $like->delete();
        }

        return response()->json([
            'liked' => false,
            'likes_count' => $photo->likes()->count(),
        ]);
    }
}