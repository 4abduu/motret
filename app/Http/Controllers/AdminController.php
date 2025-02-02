<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Comment;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = User::getUserCount();
        $photoCount = Photo::getPhotoCount();
        $commentCount = Comment::getCommentCount();
        return view('admin.dashboard', compact('userCount', 'photoCount', 'commentCount'));
    }

    public function manageUsers()
    {
        $users = User::all();
        return view('admin.manageUsers', compact('users'));
    }

    public function managePhotos()
    {
        $photos = Photo::all();
        return view('admin.managePhotos', compact('photos'));
    }
    public function manageReports()
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

    public function manageComments()
    {
        $commentCount = Comment::count();
        $replyCount = Comment::count();
        return view('admin.manageComments', compact('commentCount', 'replyCount'));
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
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.editUser', compact('user'));
    }

    public function createUser(Request $request)
    {
        $messages = [
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.min' => 'Username minimal harus 4 karakter.',
            'username.max' => 'Username maksimal 20 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
        ];

        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users,username|min:4|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role' => 'required|in:admin,pro,user',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ], $messages);

        $user = new User();
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];

        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->storeAs(
                'public/photo_profile',
                Str::random(40) . '.' . $request->file('profile_photo')->getClientOriginalExtension()
            );
            $user->profile_photo = basename($profilePhotoPath);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|min:4|max:20|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'role' => 'required|in:admin,pro,user',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->name = $validated['name'];
            $user->username = $validated['username'];
            $user->email = $validated['email'];
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
            }
            $user->role = $validated['role'];

            if ($request->hasFile('profile_photo')) {
                // Hapus foto profil lama jika ada
                if ($user->profile_photo) {
                    Storage::delete('public/photo_profile/' . $user->profile_photo);
                }

                // Simpan foto profil baru dengan nama acak
                $profilePhotoPath = $request->file('profile_photo')->storeAs(
                    'public/photo_profile',
                    Str::random(40) . '.' . $request->file('profile_photo')->getClientOriginalExtension()
                );

                $user->profile_photo = basename($profilePhotoPath);
            }

            $user->save();

            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to update user.');
        }
    }

    public function deleteUser($id)
    {
        try {
            User::findOrFail($id)->delete();
            return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Failed to delete user.');
        }
    }

    public function editPhoto($id, Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'nullable',
            'hashtags' => 'nullable',
        ]);

        try {
            $photo = Photo::findOrFail($id);
            $photo->update($validated);
            return redirect()->route('admin.photos')->with('success', 'Photo updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.photos')->with('error', 'Failed to update photo.');
        }
    }

    public function deletePhoto($id)
    {
        try {
            Photo::findOrFail($id)->delete();
            return redirect()->route('admin.photos')->with('success', 'Photo deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.photos')->with('error', 'Failed to delete photo.');
        }
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->back()->with('success', 'Laporan berhasil dihapus.');
    }

    public function banPhoto(Request $request, $id)
    {
        try {
            $photo = Photo::findOrFail($id);

            if ($photo->banned) {
                return redirect()->route('admin.reports.photos')->with('warning', 'Postingan ini telah dibanned.');
            }

            // Ambil alasan ban dari laporan terkait
            $report = Report::where('photo_id', $id)->first();
            if (!$report) {
                return redirect()->route('admin.reports.photos')->with('error', 'Laporan tidak ditemukan.');
            }

            $photo->banned = true;
            $photo->ban_expires_at = Carbon::now()->addDays(7);
            $photo->save();

            // Update semua laporan terkait dengan status banned
            Report::where('photo_id', $id)->update(['status' => true]);

            return redirect()->route('admin.reports.photos')->with('success', 'Foto berhasil dibanned.');
        } catch (\Exception $e) {
            return redirect()->route('admin.reports.photos')->with('error', 'Foto gagal dibanned: ' . $e->getMessage());
        }
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

    public function deleteProfilePhoto($id)
    {
        try {
            $user = User::findOrFail($id); // Cari user berdasarkan ID
    
            if ($user->profile_photo) { // Cek apakah user memiliki foto profil
                Storage::delete('public/photo_profile/' . $user->profile_photo); // Hapus foto profil dari penyimpanan
                $user->profile_photo = null; // Set kolom profile_photo menjadi null
                $user->save(); // Simpan perubahan
            }
    
            return redirect()->route('admin.users')->with('success', 'Foto profil berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Gagal menghapus foto profil.');
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