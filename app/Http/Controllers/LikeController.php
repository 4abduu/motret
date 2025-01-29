<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Like;
use App\Models\Notif; // Pastikan model Notif diimport
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

            // Tambahkan notifikasi
            Notif::create([
                'notify_for' => $photo->user_id,
                'notify_from' => $user->id,
                'target_id' => $photo->id,
                'type' => 'like',
                'message' => $user->username . ' liked your photo.',
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

            // Hapus notifikasi
            Notif::where('notify_from', $user->id)
                ->where('type', 'like')
                ->where('target_id', $photo->id)
                ->delete();
        }

        return response()->json([
            'liked' => false,
            'likes_count' => $photo->likes()->count(),
        ]);
    }
}