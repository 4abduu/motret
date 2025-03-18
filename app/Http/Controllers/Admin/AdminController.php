<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Photo;
use App\Models\Comment;
use App\Models\Transaction;
use App\Models\Report;
use App\Models\Reply;
use App\Models\VerificationRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Ambil data dari database
        $userCount = User::getUserCount();
        $photoCount = Photo::getPhotoCount();
        $commentCount = Comment::getCommentCount();
        $replyCount = Reply::getRepliesCount();
        $reportCount = Report::getReportCount();
        $transactionCount = Transaction::getTransactionCount();
        $verificationCount = VerificationRequest::getVerificationCount();

        // Hitung persentase perubahan
        $userPercentage = $this->calculatePercentageChange(User::class);
        $photoPercentage = $this->calculatePercentageChange(Photo::class);
        $commentPercentage = $this->calculatePercentageChange(Comment::class);
        $replyPercentage = $this->calculatePercentageChange(Reply::class);
        $reportPercentage = $this->calculatePercentageChange(Report::class);
        $transactionPercentage = $this->calculatePercentageChange(Transaction::class);
        $verificationPercentage = $this->calculatePercentageChange(VerificationRequest::class);

        // Data untuk chart (user dan foto)
        $userGrowthData = $this->getUserGrowthData();
        $photoUploadData = $this->getPhotoUploadData();

        // Data terbaru (recent activities)
        $recentUsers = User::latest()->take(1)->get();
        $recentPhotos = Photo::with('user')->latest()->take(1)->get();
        $recentComments = Comment::with('user')->latest()->take(1)->get();
        $recentReports = Report::with('user')->latest()->take(1)->get();
        $recentVerifications = VerificationRequest::with('user')->latest()->take(1)->get();
        $recentTransactions = Transaction::with('user')->latest()->take(1)->get();

        // Kirim data ke view
        return view('admin.dashboard', compact(
            'userCount',
            'photoCount',
            'commentCount',
            'replyCount',
            'reportCount',
            'transactionCount',
            'verificationCount',
            'userPercentage',
            'photoPercentage',
            'commentPercentage',
            'replyPercentage',
            'reportPercentage',
            'transactionPercentage',
            'verificationPercentage',
            'userGrowthData',
            'photoUploadData',
            'recentUsers',
            'recentPhotos',
            'recentComments',
            'recentReports',
            'recentVerifications',
            'recentTransactions'
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
    

    // Data pertumbuhan user (7 hari terakhir)
    private function getUserGrowthData()
    {
        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = User::whereDate('created_at', $date)->count();
            $labels[] = Carbon::now()->subDays($i)->format('M j');
            $data[] = $count;
        }
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // Data upload foto (7 hari terakhir)
    private function getPhotoUploadData()
    {
        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = Photo::whereDate('created_at', $date)->count();
            $labels[] = Carbon::now()->subDays($i)->format('M j');
            $data[] = $count;
        }
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
}