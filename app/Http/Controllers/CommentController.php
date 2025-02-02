<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Photo;
use App\Models\Notif; // Pastikan model Notif diimport
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store($id, Request $request)
    {
        $photo = Photo::find($id);
        Comment::create([
            'comment' => $request->comment,
            'user_id' => auth()->user()->id,
            'photo_id' => $id
        ]);

        Notif::create([
            'notify_for' => $photo->user_id,
            'notify_from' => auth()->user()->id,
            'target_id' => $id,
            'type' => 'comment',
        ]);

        return redirect()->back()->with('success', "Anda telah menambah komentar");
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        return redirect()->route('admin.comments')->with('success', 'Comment deleted successfully.');
    }

    public function storeReply($commentId, Request $request)
    {
        $comment = Comment::findOrFail($commentId);

        $reply = Reply::create([
            'reply' => $request->reply,
            'user_id' => Auth::id(),
            'comment_id' => $commentId,
        ]);

        Notif::create([
            'notify_for' => $comment->user_id,
            'notify_from' => Auth::id(),
            'target_id' => $comment->photo_id,
            'type' => 'reply',
        ]);

        return response()->json([
            'success' => true,
            'reply' => [
                'user' => [
                    'username' => Auth::user()->username,
                ],
                'reply' => $reply->reply,
                'created_at' => $reply->created_at->diffForHumans(),
            ],
        ]);
    }
}