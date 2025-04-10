<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Photo;
use App\Models\Album;
use App\Models\Comment;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.manageUsers', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.editUser', compact('user'));
    }
    public function previewProfile($id)
    {
        $user = User::findOrFail($id);
        $photos = $user->photos()->where('premium', false)->get();
        $premiumPhotos = $user->photos()->where('premium', true)->get();
        $albums = $user->albums()->with(['photos' => function ($query) {
            $query->where('status', 1); // Hanya foto publik
        }])->get();
        $hasSubscriptionPrice = $user->subscriptionPrice()->exists(); // Cek apakah user memiliki harga langganan
        $subscribers = $user->subscribers()->with('user')->get(); // Ambil data subscriber
    
        return view('admin.preview.profile', compact('user', 'photos', 'premiumPhotos', 'albums', 'hasSubscriptionPrice', 'subscribers'));
    }

    public function previewPhotos($id)
    {
        $photo = Photo::findOrFail($id);
        return view('admin.preview.photos', compact('photo'));
    }

    public function previewAlbum($albumId)
    {
        $album = Album::with('photos')->findOrFail($albumId);
        return view('admin.preview.albums', compact('album'));
    }

    public function previewCommentReplies($id, $type)
    {
        Log::info("Type: $type, ID: $id");
    
        if ($type === 'comment') {
            $comment = Comment::with(['photo.user', 'replies.user', 'user'])->findOrFail($id);
            $highlighted = $comment;
            $parentComment = null;
        } elseif ($type === 'reply') {
            $reply = Reply::with(['comment.photo.user', 'comment.replies.user', 'user'])->findOrFail($id);
            $highlighted = $reply;
            $parentComment = $reply->comment;
        } else {
            abort(404, 'Invalid preview type');
        }
    
        return view('admin.preview.comments', compact('highlighted', 'parentComment', 'type'));
    }

    public function createUser(Request $request)
    {
        $messages = [
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.min' => 'Username minimal harus 4 karakter.',
            'username.max' => 'Username maksimal 20 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf kecil, angka, titik, dan underscore.',
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
            'username' => [
                'required',
                'unique:users,username',
                'min:4',
                'max:20',
                'regex:/^[a-z0-9._]+$/', // Hanya huruf kecil, angka, titik, dan underscore
            ],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'role' => 'required|in:admin,pro,user',
            'bio' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ], $messages);
    
        try {
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
    
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil dibuat.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat pengguna. Pesan: ' . $e->getMessage()]);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'username' => [
                'required',
                'min:4',
                'max:20',
                'unique:users,username,' . $id,
                'regex:/^[a-z0-9._]+$/', // Hanya huruf kecil, angka, titik, dan underscore
            ],
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'role' => 'required|in:admin,pro,user',
            'bio' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
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
            $user->bio = $validated['bio'];
            $user->website = $validated['website'];
    
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
    
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pengguna. Pesan: ' . $e->getMessage()]);
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

    public function deleteUser($id)
    {
        try {
            User::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete user.'], 500);
        }
    }

    public function banUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->banned_type = $request->banned_type;
        $user->banned_until = $request->banned_type === 'temporary' ? $request->banned_until : null;
        $user->banned_reason = $request->banned_reason;
        $user->banned = true;
        $user->save();
    
        return response()->json(['message' => 'User has been banned successfully.']);
    }
}