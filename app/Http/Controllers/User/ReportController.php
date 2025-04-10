<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
    
        $reason = $validated['reason'] === 'Lainnya' ? $validated['description'] : $validated['reason'];
    
        Report::create([
            'user_id' => Auth::id(),
            'photo_id' => $photoId,
            'reason' => $reason,
        ]);

        if (!$request->ajax()) {
            return redirect()->back()->with('success', 'Foto berhasil dilaporkan.');
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dilaporkan.',
        ]);
    }

    public function reportComment(Request $request, $commentId)
    {
        
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
    
        $reason = $validated['reason'] === 'Lainnya' ? $validated['description'] : $validated['reason'];
    
        Report::create([
            'user_id' => Auth::id(),
            'comment_id' => $commentId,
            'reason' => $reason,
        ]);

        if (!$request->ajax()) {
            return redirect()->back()->with('success', 'Komentar berhasil dilaporkan.');
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil dilaporkan.',
        ]);
    }

    public function reportReply(Request $request, $replyId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $reason = $validated['reason'] === 'Lainnya' ? $validated['description'] : $validated['reason'];

        Report::create([
            'user_id' => Auth::id(),
            'reply_id' => $replyId,
            'reason' => $reason,
        ]);

        if (!$request->ajax()) {
            return redirect()->back()->with('success', 'Balasan berhasil dilaporkan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Balasan berhasil dilaporkan.',
        ]);
    }

    public function reportUser(Request $request, $userId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $reason = $validated['reason'] === 'Lainnya' ? $validated['description'] : $validated['reason'];

        Report::create([
            'user_id' => Auth::id(),
            'reported_user_id' => $userId,
            'reason' => $reason,
        ]);

        if (!$request->ajax()) {
            return redirect()->back()->with('success', 'Pengguna berhasil dilaporkan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dilaporkan.',
        ]);
    }
}
