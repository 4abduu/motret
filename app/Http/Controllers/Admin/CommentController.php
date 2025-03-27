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
        // Jumlah data 
        $commentCount = Comment::getCommentCount();
        $replyCount = Reply::getRepliesCount();
        $recentComments = Comment::with('user')->latest()->take(5)->get();
        $recentReplies = Reply::with('user')->latest()->take(5)->get();

        // Hitung persentase
        $commentPercentage = $this->calculatePercentageChange(Comment::class);
        $replyPercentage = $this->calculatePercentageChange(Reply::class);

        return view('admin.manageComments', compact(
            'commentCount',
            'replyCount',
            'recentComments',
            'recentReplies',
            'commentPercentage',
            'replyPercentage'
        ));
    }

    private function calculatePercentageChange($model)
    {
        // Hitung jumlah data 7 hari terakhir
        $last7DaysCount = $model::where('created_at', '>=', Carbon::now()->subDays(7))->count();
    
        // Hitung jumlah data 7 hari sebelumnya (8-14 hari yang lalu)
        $previous7DaysCount = $model::whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();
    
        // Jika periode sebelumnya 0 dan sekarang ada data, maka 100% kenaikan
        if ($previous7DaysCount == 0) {
            return $last7DaysCount > 0 ? "+100%" : "0%";
        }
    
        // Hitung persentase perubahan
        $percentageChange = (($last7DaysCount - $previous7DaysCount) / $previous7DaysCount) * 100;
    
        // Tambahkan tanda "+" jika ada kenaikan
        $formattedPercentage = round($percentageChange, 2);
        if ($formattedPercentage > 0) {
            return "+" . $formattedPercentage . "%";
        } elseif ($formattedPercentage < 0) {
            return $formattedPercentage . "%"; // Tanda minus otomatis sudah ada
        } else {
            return "0%";
        }
    }

    public function comments()
    {
        $comments = Comment::all();
        return view('admin.comments.comments', compact('comments'));
    }

    public function replies()
    {
        $replies = Reply::all();
        return view('admin.comments.replies', compact('replies'));
    }

    public function banComment(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
    
            if ($comment->banned) {
                return response()->json(['message' => 'Komentar ini telah dibanned.'], 400);
            }
    
            $report = Report::where('comment_id', $id)->first();
            if (!$report) {
                return response()->json(['message' => 'Laporan tidak ditemukan.'], 404);
            }
    
            $comment->banned = true;
            $comment->ban_expires_at = Carbon::now()->addDays(7);
            $comment->save();
    
            Report::where('comment_id', $id)->update(['status' => true]);
    
            return response()->json(['message' => 'Komentar berhasil dibanned.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Komentar gagal dibanned: ' . $e->getMessage()], 500);
        }
    }
    
    public function banReplies(Request $request, $id)
    {
        try {
            $reply = Reply::findOrFail($id);
    
            if ($reply->banned) {
                return response()->json(['message' => 'Balasan ini telah dibanned.'], 400);
            }
    
            $report = Report::where('reply_id', $id)->first();
            if (!$report) {
                return response()->json(['message' => 'Laporan tidak ditemukan.'], 404);
            }
    
            $reply->banned = true;
            $reply->ban_expires_at = Carbon::now()->addDays(7);
            $reply->save();
    
            Report::where('reply_id', $id)->update(['status' => true]);
    
            return response()->json(['message' => 'Balasan berhasil dibanned.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Balasan gagal dibanned: ' . $e->getMessage()], 500);
        }
    }
    
    public function deleteComment($id)
    {
        try {
            Comment::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Comment deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete comment.']);
        }
    }
    public function deleteReply($id)
    {
        try {
            Reply::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Reply deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete reply.']);
        }
    }
}
