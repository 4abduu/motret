<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil jumlah report berdasarkan jenis
        $reportUserCount = Report::whereNotNull('reported_user_id')->count();
        $reportCommentCount = Report::whereNotNull('comment_id')->count();
        $reportReplyCount = Report::whereNotNull('reply_id')->count();
        $reportPhotoCount = Report::whereNotNull('photo_id')->count();

        // Ambil data terbaru untuk recent activities
        $recentReports = Report::with(['user', 'reportedUser', 'photo', 'comment', 'reply'])
            ->latest()
            ->take(10)
            ->get();

        // Kirim data ke view
        return view('admin.manageReports', compact(
            'reportUserCount',
            'reportCommentCount',
            'reportReplyCount',
            'reportPhotoCount',
            'recentReports'
        ));
    }
    public function reportUsers()
    {
        $reportUsers = Report::whereNotNull('reported_user_id')->get();
        return view('admin.reportUsers', compact('reportUsers'));
    }

    public function reportComments()
    {
        $reportComments = Report::whereNotNull('comment_id')->get();
        $reportReplies = Report::whereNotNull('reply_id')->get();
        return view('admin.reportComments', compact('reportComments', 'reportReplies'));
    }

    public function reportPhotos()
    {
        $reportPhotos = Report::whereNotNull('photo_id')->get();
        return view('admin.reportPhotos', compact('reportPhotos'));
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    }
}
