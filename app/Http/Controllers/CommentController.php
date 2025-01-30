<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
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

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}