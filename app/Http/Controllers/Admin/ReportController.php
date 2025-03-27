<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil jumlah report berdasarkan jenis
        $reportUserCount = Report::whereNotNull('reported_user_id')->count();
        $reportCommentCount = Report::whereNotNull('comment_id')->count();
        $reportReplyCount = Report::whereNotNull('reply_id')->count();
        $reportPhotoCount = Report::whereNotNull('photo_id')->count();

        // Hitung persentase perubahan jumlah report 7 hari terakhir
        $reportUserPercentage = $this->calculatePercentageChange(Report::whereNotNull('reported_user_id'));
        $reportCommentPercentage = $this->calculatePercentageChange(Report::whereNotNull('comment_id'));
        $reportReplyPercentage = $this->calculatePercentageChange(Report::whereNotNull('reply_id'));
        $reportPhotoPercentage = $this->calculatePercentageChange(Report::whereNotNull('photo_id'));

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
            'recentReports',
            'reportUserPercentage',
            'reportCommentPercentage',
            'reportReplyPercentage',
            'reportPhotoPercentage'
        ));
    }

    private function calculatePercentageChange($model)
    {
        // Hitung jumlah data 7 hari terakhir
        $last7DaysCount = $model->where('created_at', '>=', Carbon::now()->subDays(7))->count();
    
        // Hitung jumlah data 7 hari sebelumnya (8-14 hari yang lalu)
        $previous7DaysCount = $model->whereBetween('created_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])->count();
    
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

    public function reportUsers()
    {
        $reportUsers = Report::whereNotNull('reported_user_id')->get();
        return view('admin.reports.reportUsers', compact('reportUsers'));
    }

    public function reportComments()
    {
        $reportComments = Report::whereNotNull('comment_id')->get();
        $reportReplies = Report::whereNotNull('reply_id')->get();
        return view('admin.reports.reportComments', compact('reportComments', 'reportReplies'));
    }

    public function reportPhotos()
    {
        $reportPhotos = Report::whereNotNull('photo_id')->get();
        return view('admin.reports.reportPhotos', compact('reportPhotos'));
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
    
        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }
}
