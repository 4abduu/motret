<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notif;
use App\Models\Photo;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\User;
use App\Models\Follow;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class LCFNController extends Controller
{
    /**
     * Constructor untuk mengatur middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menyimpan foto yang disukai oleh pengguna.
     *
     * @param int $photoId
     * @return \Illuminate\Http\JsonResponse
     */
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
                    'message' => 'menyukai foto Anda.',
                ]);
        }

        return response()->json([
            'liked' => true,
            'likes_count' => $photo->likes()->count(),
        ]);
    }

    /**
     * Menghapus foto yang tidak disukai oleh pengguna.
     *
     * @param int $photoId
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Menyimpan komentar pada foto.
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeComment($id, Request $request)
    {
        $request->validate([
            'comment' => 'required|string|max:500'
        ]);
    
        $photo = Photo::findOrFail($id);
        $user = Auth::user();
    
        $comment = Comment::create([
            'comment' => $request->comment,
            'user_id' => $user->id,
            'photo_id' => $id
        ]);
    
        // Buat notifikasi
        Notif::create([
            'notify_for' => $photo->user_id,
            'notify_from' => $user->id,
            'target_id' => $photo->id,
            'type' => 'comment',
            'message' => 'mengomentari foto Anda.',
        ]);
    
        $response = ([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'profile_photo' => $user->profile_photo,
                    'verified' => $user->verified,
                    'role' => $user->role,
                ],
                'replies' => []
            ]
        ]);

        Log::info('Reply created:', $response);

        return response()->json($response);

    }
    
    /**
     * Menghapus komentar pada foto.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);
    
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        $comment->delete();
    
        return response()->json(['success' => true]);
    }

    /**
     * Menyimpan balasan komentar.
     *
     * @param int $commentId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeReply($commentId, Request $request)
    {
        $comment = Comment::findOrFail($commentId);
        $user = Auth::user();
        $photo = Photo::findOrFail($comment->photo_id);
    
        $reply = Reply::create([
            'reply' => $request->input('reply'), // Gunakan input() untuk lebih aman
            'user_id' => $user->id,
            'comment_id' => $commentId,
        ]);
    
        Notif::create([
            'notify_for' => $comment->user_id,
            'notify_from' => $user->id,
            'target_id' => $comment->photo_id,
            'type' => 'reply',
            'message' => 'membalas komentar Anda.',
        ]);
    
        $response = [
            'success' => true,
            'reply' => [
                'id' => $reply->id,
                'reply' => $reply->reply,
                'created_at' => $reply->created_at->diffForHumans(),
                'user' => [
                    'id' => $reply->user->id,
                    'username' => $reply->user->username,
                    'profile_photo' => $reply->user->profile_photo,
                    'verified' => $reply->user->verified,
                    'role' => $reply->user->role,
                ],
            ],
            'photoUserId' => $photo->user_id,
        ];
    
        Log::info('Reply created:', $response);
        
        return response()->json($response);
    }

    /**
     * Menghapus balasan komentar.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyReply($id)
    {
        $reply = Reply::findOrFail($id);

        if ($reply->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }
        
        $reply->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Mengikuti pengguna lain.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Menghapus pengguna yang diikuti.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
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

        /**
     * Menampilkan halaman notifikasi pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function notification()
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
}