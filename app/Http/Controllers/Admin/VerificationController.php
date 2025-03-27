<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use App\Models\Notif;

class VerificationController extends Controller
{
    public function index()
    {
        $verificationRequests = VerificationRequest::with('user', 'documents')->get();
        return view('admin.verifications.verificationRequests', compact('verificationRequests'));
    }
    public function showVerificationDocuments($id)
    {
        $verificationRequest = VerificationRequest::with('documents')->findOrFail($id);
        return view('admin.verifications.verificationDocuments', compact('verificationRequest'));
    }

    public function deleteVerificationRequest($id)
    {
        try {
            $verificationRequest = VerificationRequest::with('documents')->findOrFail($id);

            // Hapus dokumen terkait
            foreach ($verificationRequest->documents as $document) {
                if (file_exists(storage_path('app/public/' . $document->file_path))) {
                    unlink(storage_path('app/public/' . $document->file_path));
                }
                $document->delete();
            }

            // Hapus permintaan verifikasi
            $verificationRequest->delete();

            return response()->json(['success' => true, 'message' => 'Permintaan verifikasi berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal menghapus permintaan verifikasi.']);
        }
    }

    public function rejectVerificationRequest(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);
        $user = $verificationRequest->user;

        $verificationRequest->status = 'rejected';
        $verificationRequest->save();

        Notif::create([
            'notify_for' => $user->id,
            'notify_from' => null,
            'target_id' => $user->id,
            'type' => 'system',
            'message' => 'Permintaan verifikasi Anda telah ditolak. Pesan: ' . $request->message,
        ]);

        return redirect()->route('admin.verificationRequests')->with('success', 'Permintaan verifikasi telah ditolak.');
    }

    
    public function approveVerificationRequest($id)
    {
        $verificationRequest = VerificationRequest::findOrFail($id);
        $user = $verificationRequest->user;
        $user->verified = true;
        $user->save();

        $verificationRequest->status = 'approved';
        $verificationRequest->save();

        Notif::create([
            'notify_for' => $user->id,
            'notify_from' => null,
            'target_id' => $user->id,
            'type' => 'system',
            'message' => 'Permintaan verifikasi Anda telah disetujui.',
        ]);

        return redirect()->route('admin.verificationRequests')->with('success', 'Permintaan verifikasi telah disetujui.');
    }
}
