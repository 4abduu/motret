<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        $reportUserCount = Report::whereNotNull('reported_user_id')->count();
        $reportCommentCount = Report::whereNotNull('comment_id')->count();
        $reportPhotoCount = Report::whereNotNull('photo_id')->count();
        return view('admin.manageReports', compact('reportUserCount', 'reportCommentCount', 'reportPhotoCount'));
    }
    public function reportUsers()
    {
        $reportUsers = Report::whereNotNull('reported_user_id')->get();
        return view('admin.reportUsers', compact('reportUsers'));
    }

    public function reportComments()
    {
        $reportComments = Report::whereNotNull('comment_id')->get();
        return view('admin.reportComments', compact('reportComments'));
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
