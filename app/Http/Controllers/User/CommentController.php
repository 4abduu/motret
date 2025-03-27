<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Photo;
use App\Models\Notif;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store($id, Request $request)
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
    
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
    
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        $comment->delete();
    
        return response()->json(['success' => true]);
    }


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

    public function destroyReply($id)
{
    $reply = Reply::findOrFail($id);

    if ($reply->user_id !== Auth::id()) {
        return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
    }
    
    $reply->delete();

    return response()->json(['success' => true]);
}

    
}