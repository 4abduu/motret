<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function reportPhoto(Request $request, $photoId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
    
        try {
            $reason = $validated['reason'] === 'Lainnya' 
                ? $validated['description'] 
                : $validated['reason'];
    
            Report::create([
                'user_id' => Auth::id(),
                'photo_id' => $photoId,
                'reason' => $reason,
            ]);
    
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Foto berhasil dilaporkan.'])
                : redirect()->back()->with('success', 'Foto berhasil dilaporkan.');
    
        } catch (\Exception $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Gagal melaporkan foto.'], 500)
                : redirect()->back()->with('error', 'Gagal melaporkan foto.');
        }
    }

    public function reportComment(Request $request, $commentId)
    {

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $reason = $validated['reason'] === 'Lainnya' 
                ? $validated['description'] 
                : $validated['reason'];
    
                Report::create([
                    'user_id' => Auth::id(),
                    'comment_id' => $commentId,
                    'reason' => $reason,
                ]);
    
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Komentar berhasil dilaporkan.'])
                : redirect()->back()->with('success', 'Komentar berhasil dilaporkan.');
    
        } catch (\Exception $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Gagal melaporkan komentar.'], 500)
                : redirect()->back()->with('error', 'Gagal melaporkan komentar.');
        }
    }

    public function reportReply(Request $request, $replyId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $reason = $validated['reason'] === 'Lainnya' 
                ? $validated['description'] 
                : $validated['reason'];
    
                Report::create([
                    'user_id' => Auth::id(),
                    'reply_id' => $replyId,
                    'reason' => $reason,
                ]);
    
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Balasan berhasil dilaporkan.'])
                : redirect()->back()->with('success', 'Balasan berhasil dilaporkan.');
    
        } catch (\Exception $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Gagal melaporkan balasan.'], 500)
                : redirect()->back()->with('error', 'Gagal melaporkan balasan.');
        }
    }

    public function reportUser(Request $request, $userId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $reason = $validated['reason'] === 'Lainnya' 
                ? $validated['description'] 
                : $validated['reason'];
    
                Report::create([
                    'user_id' => Auth::id(),
                    'reported_user_id' => $userId,
                    'reason' => $reason,
                ]);
    
            return $request->expectsJson()
                ? response()->json(['success' => true, 'message' => 'Pengguna berhasil dilaporkan.'])
                : redirect()->back()->with('success', 'Pengguna berhasil dilaporkan.');
    
        } catch (\Exception $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Gagal melaporkan pengguna.'], 500)
                : redirect()->back()->with('error', 'Gagal melaporkan pengguna.');
        }
    }
}
