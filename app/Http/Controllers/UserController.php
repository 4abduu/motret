<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Download;
use App\Models\User;
use App\Models\Report;
use App\Models\Notif;
use App\Models\Comment;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;  // Mengganti nama alias
use Illuminate\Support\Facades\DB;
use App\Models\VerificationRequest;
use App\Models\VerificationDocument;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showPhoto', 'downloadPhoto', 'showProfile']);
    }

    public function profile()
    {
        $user = Auth::user();
        $photos = Photo::where('user_id', $user->id)->get();
        $albums = Album::where('user_id', $user->id)->with('photos')->get();
        return view('user.profile', compact('user', 'photos', 'albums'));
    }

    public function showProfile($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $photos = Photo::where('user_id', $user->id)->get();
        $albums = Album::where('user_id', $user->id)->with('photos')->get();
        return view('user.profile', compact('user', 'photos', 'albums'));
    }

    public function photos()
    {
        $photos = Photo::where('user_id', Auth::id())->get();
        $albums = Album::where('user_id', Auth::id())->with('photos')->get();
        return view('user.photos', compact('photos', 'albums'));
    }    
    public function settings()
    {
        return view('user.settings');
    }
    public function showPhoto($id)
    {
        $photo = Photo::with('user')->findOrFail($id);
        $photo->increment('views');
        $randomPhotos = Photo::where('id', '!=', $id)
                             ->where('banned', false)
                             ->inRandomOrder()
                             ->take(4)
                             ->get();
        $albums = Auth::check() ? Album::where('user_id', Auth::id())->with('photos')->get() : [];
        return view('photos.show', compact('photo', 'randomPhotos', 'albums'));
    }


    public function subscription()
    {
        $user = Auth::user();
        return view ('user.subscription');
    }

    public function createphotos()
    {
        return view('photos.create');
    }

    public function storePhoto(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'hashtags' => 'required|string',
            'premium' => 'required|boolean',
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        // Buat versi buram dari foto
        $lowResDir = storage_path('app/public/low_res_photos');
        if (!file_exists($lowResDir)) {
            mkdir($lowResDir, 0775, true);
        }
        $lowResPath = $lowResDir . '/' . basename($path);
        $image = Image::make(storage_path('app/public/' . $path));
        $image->blur(50);
        $image->save($lowResPath);

        Photo::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'path' => $path,
            'hashtags' => json_encode(explode(',', $validated['hashtags'])),
            'premium' => $request->premium,
            'status' => '1',
        ]);

        return redirect()->route('home')->with('success', 'Foto berhasil diunggah.');
    }

    public function downloadPhoto(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $user = Auth::user();
        $guestDownloadCount = session('guest_download_count', 0);

        // Cek role
        if ($user) {
            if ($user->role === 'pro') {
                Download::create(['user_id' => $user->id, 'photo_id' => $photo->id, 'resolution' => 'original']);
                return $this->processDownload($photo, 'original');
            } elseif ($user->role === 'user') {
                // Cek apakah perlu reset download count
                if ($user->download_reset_at === null || Carbon::now()->greaterThan($user->download_reset_at)) {
                    $user->download_reset_at = Carbon::now()->addWeek();
                    $user->save();

                    Download::where('user_id', $user->id)->delete();
                }

                $downloadCount = Download::where('user_id', $user->id)
                                        ->where('created_at', '>=', now()->startOfWeek())
                                        ->count();

                if ($downloadCount < 5) {
                    Download::create(['user_id' => $user->id, 'photo_id' => $photo->id, 'resolution' => 'original']);
                    return $this->processDownload($photo, 'original');
                } else {
                    return back()->with('error', 'Anda telah mencapai batas download minggu ini.');
                }
            }
        } else {
            if ($guestDownloadCount < 5) {
                session(['guest_download_count' => $guestDownloadCount + 1]);
                Download::create(['user_id' => null, 'photo_id' => $photo->id, 'resolution' => 'low']);
                return $this->processDownload($photo, 'low');
            } else {
                return back()->with('error', 'Anda telah mencapai batas download sebagai tamu.');
            }
        }
    }

    private function processDownload($photo, $resolution)
    {
        $filePath = storage_path('app/public/' . $photo->path);
        if ($resolution === 'low') {
            // Path untuk versi buram gambar
            $lowResPath = storage_path('app/public/low_res_photos/' . basename($photo->path));
            
            // Memastikan file buram ada
            if (!file_exists($lowResPath)) {
                Log::error('File buram tidak ditemukan.', ['filePath' => $lowResPath]);
                return back()->with('error', 'File tidak ditemukan.');
            }

            // Bersihkan buffer output sebelum mengirim file
            if (ob_get_length()) {
                ob_end_clean();
            }
            return response()->streamDownload(function () use ($lowResPath) {
                readfile($lowResPath);
            }, basename($photo->path), [
                'Content-Type' => mime_content_type($lowResPath)
            ]);
        }

        // Memastikan file asli ada
        if (!file_exists($filePath)) {
            Log::error('File asli tidak ditemukan.', ['filePath' => $filePath]);
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Bersihkan buffer output sebelum mengirim file
        if (ob_get_length()) {
            ob_end_clean();
        }
        return response()->streamDownload(function () use ($filePath) {
            readfile($filePath);
        }, basename($photo->path), [
            'Content-Type' => mime_content_type($filePath)
        ]);
    }

    public function editPhoto($id)
    {
        $photo = Photo::findOrFail($id);
        if (Auth::id() !== $photo->user_id) {
            return redirect()->route('user.profile')->with('error', 'Anda tidak memiliki izin untuk mengedit foto ini.');
        }
        return view('photos.edit', compact('photo'));
    }

    public function updatePhoto(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        if (Auth::id() !== $photo->user_id) {
            return redirect()->route('user.profile')->with('error', 'Anda tidak memiliki izin untuk mengedit foto ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'hashtags' => 'required|string',
        ]);

        $photo->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'hashtags' => json_encode(explode(',', $validated['hashtags'])),
        ]);

        return redirect()->route('user.profile')->with('success', 'Foto berhasil diperbarui.');
    }

    public function destroyPhoto($id)
    {
        $photo = Photo::findOrFail($id);
        if (Auth::id() !== $photo->user_id) {
            return redirect()->route('user.profile')->with('error', 'Anda tidak memiliki izin untuk menghapus foto ini.');
        }

        $photo->delete();
        return redirect()->route('user.profile')->with('success', 'Foto berhasil dihapus.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

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

        $user->name = $validated['name'];
        $user->bio = $validated['bio'];
        $user->website = $validated['website'];
        $user->save();

        return redirect()->route('user.showProfile', $user->username)->with('success', 'Profil berhasil diperbarui.');
    }
    // public function updateProfile(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'username' => 'required|string|max:20|unique:users,username,' . Auth::id(),
    //         'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
    //         'current_password' => 'nullable|string',
    //         'new_password' => 'nullable|string|min:8|confirmed',
    //         'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif',
    //     ]);

    //     $user = Auth::user();
    //     $user->name = $validated['name'];
    //     $user->username = $validated['username'];
    //     $user->email = $validated['email'];

    //     if ($request->filled('current_password') && $request->filled('new_password')) {
    //         if (Hash::check($request->current_password, $user->password)) {
    //             $user->password = Hash::make($request->new_password);
    //         } else {
    //             return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
    //         }
    //     }

    //     if ($request->hasFile('profile_photo')) {
    //         // Hapus foto profil lama jika ada
    //         if ($user->profile_photo) {
    //             Storage::delete('public/photo_profile/' . $user->profile_photo);
    //         }

    //         // Simpan foto profil baru dengan nama acak
    //         $profilePhotoPath = $request->file('profile_photo')->storeAs(
    //             'public/photo_profile',
    //             Str::random(40) . '.' . $request->file('profile_photo')->getClientOriginalExtension()
    //         );

    //         $user->profile_photo = basename($profilePhotoPath);
    //     }

    //     $user->save();

    //     return redirect()->route('user.profile')->with('success', 'Profil berhasil diperbarui.');
    // }

    public function deleteProfilePhoto()
    {
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::delete('public/photo_profile/' . $user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return redirect()->route('user.profile')->with('success', 'Foto profil berhasil dihapus.');
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
    
        return redirect()->route('photos.show', $photoId)->with('success', 'Foto berhasil dilaporkan.');
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

        return redirect()->back()->with('success', 'Komentar berhasil dilaporkan.');
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
    
        $reportedUser = User::findOrFail($userId);
    
        return redirect()->route('user.showProfile', ['username' => $reportedUser->username])->with('success', 'Pengguna berhasil dilaporkan.');
    }

    public function checkUsername(Request $request)
    {
        $exists = User::where('username', $request->username)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    public function updateUsername(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:20|unique:users,username,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->username = $validated['username'];
        $user->save();

        return redirect()->route('user.settings')->with('success', 'Username berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],  // Menggunakan alias PasswordRule
        ]);

        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($validated['new_password']);
            $user->save();
            return redirect()->route('user.settings')->with('success', 'Password berhasil diperbarui.');
        } else {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'old_email' => 'required|string|email|max:255',
            'new_email' => 'required|string|email|max:255|unique:users,email',
            'verification_code' => 'required|string|size:8',
        ]);

        $user = Auth::user();
        if ($user->email !== $validated['old_email']) {
            return back()->withErrors(['old_email' => 'Email lama tidak sesuai.']);
        }

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $validated['old_email'])
            ->where('token', $validated['verification_code'])
            ->where('type', 'email')
            ->first();

        if (!$passwordReset || Carbon::parse($passwordReset->created_at)->lt(Carbon::now()->subMinutes(30))) {
            return back()->withErrors(['verification_code' => 'Kode verifikasi tidak valid atau telah kadaluarsa.']);
        }

        $user->email = $validated['new_email'];
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['old_email'])->delete();

        return redirect()->route('user.settings')->with('success', 'Email berhasil diperbarui.');
    }

    public function checkVerificationUsername(Request $request)
    {
        $username = $request->input('username');
        $isValid = $username === Auth::user()->username;
    
        return response()->json(['isValid' => $isValid]);
    }
    
    public function submitVerification(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'selfie' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'portfolio' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'certificate' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'reason' => 'required|string|max:1000',
        ]);
    
        // Cek apakah username yang diinputkan sesuai dengan username yang ada di tabel users
        if ($validated['username'] !== Auth::user()->username) {
            return redirect()->back()->withErrors(['username' => 'Username tidak sesuai dengan username yang terdaftar di sistem.'])->withInput();
        }
    
        $verificationRequest = VerificationRequest::create([
            'user_id' => Auth::id(),
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);
    
        $documents = [
            'ktp' => $request->file('ktp'),
            'selfie' => $request->file('selfie'),
            'portfolio' => $request->file('portfolio'),
            'certificate' => $request->file('certificate'),
        ];
    
        foreach ($documents as $type => $file) {
            if ($file) {
                $path = $file->store('verifications/' . $type, 'public');
                VerificationDocument::create([
                    'verification_request_id' => $verificationRequest->id,
                    'file_path' => $path,
                    'file_type' => $type,
                ]);
            }
        }
    
        Notif::create([
            'notify_for' => Auth::id(),
            'notify_from' => null,
            'target_id' => Auth::id(),
            'type' => 'system',
            'message' => 'Pengajuan verifikasi Anda telah diterima dan sedang diproses.',
        ]);
    
        return redirect()->route('user.settings')->with('success', 'Pengajuan verifikasi telah dikirim.');
    }
}