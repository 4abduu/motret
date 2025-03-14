<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Photo;
use App\Models\Notif;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function store($id, Request $request)
    {
        // Ambil username pengguna yang sedang login
        $user = Auth::user()->username;
    
        // Temukan foto berdasarkan ID
        $photo = Photo::find($id);
    
        // Pastikan foto ditemukan sebelum melanjutkan
        if (!$photo) {
            return redirect()->back()->with('error', 'Foto tidak ditemukan.');
        }
    
        // Menambahkan komentar ke database
        $comment = Comment::create([
            'comment' => $request->comment,
            'user_id' => auth()->user()->id,
            'photo_id' => $id
        ]);
    
        // Menambahkan notifikasi
        Notif::create([
            'notify_for' => $photo->user_id, // Pengguna yang mendapatkan notifikasi
            'notify_from' => auth()->user()->id, // Pengguna yang mengirim notifikasi
            'target_id' => $photo->id, // ID dari foto yang dikomentari, pastikan ini sesuai dengan apa yang diinginkan
            'type' => 'comment', // Tipe notifikasi
            'message' => 'mengomentari foto Anda.', // Pesan notifikasi
        ]);
    
        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', "Anda telah menambah komentar.");
    }
    

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        return redirect()->back()->with('success', "Anda telah menghapus komentar.");
    }


    public function storeReply($commentId, Request $request)
    {
        $comment = Comment::findOrFail($commentId);
        $user = Auth::user(); // Ambil objek user yang sedang login
        $photo = Photo::findOrFail($comment->photo_id);
    
        $reply = Reply::create([
            'reply' => $request->reply,
            'user_id' => $user->id, // Gunakan ID user yang login
            'comment_id' => $commentId,
        ]);
    
        Notif::create([
            'notify_for' => $comment->user_id,
            'notify_from' => $user->id, // Pastikan mengambil user_id yang benar
            'target_id' => $comment->photo_id, // Menggunakan ID postingan sebagai target_id
            'type' => 'reply',
            'message' => 'membalas komentar Anda.',
        ]);
    
        // Format JSON response yang mau di-log
        return response()->json([
            'success' => true,
            'reply' => [
                'id' => $reply->id,
                'reply' => $reply->reply,
                'created_at' => $reply->created_at->diffForHumans(),
                'user' => [
                    'id' => $reply->user->id,
                    'username' => $reply->user->username,
                    'profile_photo' => $reply->user->profile_photo,
                    'verified' => $reply->user->verified,
                    'role' => $reply->user->role,
                ],
            ],
            'photoUserId' => $photo->user_id, // ID pembuat foto
        ]);
    
        // Simpan log ke storage/logs/laravel.log
        Log::info(json_encode($response));
    
        return response()->json($response);
    }

    public function destroyReply($id)
{
    $reply = Reply::findOrFail($id);

    if ($reply->user_id !== Auth::id()) {
        return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
    }
    
    $reply->delete();

    return response()->json(['success' => true]);
}

    
}