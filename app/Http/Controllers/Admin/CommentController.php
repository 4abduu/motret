<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Reply;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CommentController extends Controller
{
    public function index()
    {
        $commentCount = Comment::getCommentCount();
        $replyCount = Reply::getRepliesCount();
        $recentComments = Comment::with('user')->latest()->take(5)->get();
        $recentReplies = Reply::with('user')->latest()->take(5)->get();

        return view('admin.manageComments', compact(
            'commentCount',
            'replyCount',
            'recentComments',
            'recentReplies'
        ));
    }

    public function comments()
    {
        $comments = Comment::all();
        return view('admin.comments', compact('comments'));
    }

    public function replies()
    {
        $replies = Reply::all();
        return view('admin.replies', compact('replies'));
    }

    public function banComment(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if ($comment->banned) {
                return redirect()->route('admin.reports.comments')->with('warning', 'Komentar ini telah dibanned.');
            }

            // Ambil alasan ban dari laporan terkait
            $report = Report::where('comment_id', $id)->first();
            if (!$report) {
                return redirect()->route('admin.reports.comments')->with('error', 'Laporan tidak ditemukan.');
            }

            $comment->banned = true;
            $comment->ban_expires_at = Carbon::now()->addDays(7);
            $comment->save();

            // Update semua laporan terkait dengan status banned
            Report::where('comment_id', $id)->update(['status' => true]);

            return redirect()->route('admin.reports.comments')->with('success', 'Komentar berhasil dibanned.');
        } catch (\Exception $e) {
            return redirect()->route('admin.reports.comments')->with('error', 'Komentar gagal dibanned: ' . $e->getMessage());
        }
    }

    public function banReplies(Request $request, $id)
    {
        try {
            $reply = Reply::findOrFail($id);

            if ($reply->banned) {
                return redirect()->route('admin.reports.comments')->with('warning', 'Balasan ini telah dibanned.');
            }

            // Ambil alasan ban dari laporan terkait
            $report = Report::where('reply_id', $id)->first();
            if (!$report) {
                return redirect()->route('admin.reports.comments')->with('error', 'Laporan tidak ditemukan.');
            }

            $reply->banned = true;
            $reply->ban_expires_at = Carbon::now()->addDays(7);
            $reply->save();

            // Update semua laporan terkait dengan status banned
            Report::where('reply_id', $id)->update(['status' => true]);

            return redirect()->route('admin.reports.comments')->with('success', 'Komentar berhasil dibanned.');
        } catch (\Exception $e) {
            return redirect()->route('admin.reports.comments')->with('error', 'Komentar gagal dibanned: ' . $e->getMessage());
        }
    }
    
    public function deleteComment($id)
    {
        try {
            Comment::findOrFail($id)->delete();
            return redirect()->route('admin.comments')->with('success', 'Comment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.comments')->with('error', 'Failed to delete comment.');
        }
    }
    public function deleteReply($id)
    {
        try {
            Reply::findOrFail($id)->delete();
            return redirect()->route('admin.replies')->with('success', 'Reply deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.replies')->with('error', 'Failed to delete reply.');
        }
    }
}
