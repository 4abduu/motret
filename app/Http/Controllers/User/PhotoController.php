<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use App\Models\Album;
use Intervention\Image\Facades\Image;
use App\Models\Download;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showPhoto', 'downloadPhoto']);
    }

    public function index()
    {
        $photos = Photo::where('user_id', Auth::id())->get();
        $albums = Album::where('user_id', Auth::id())->with('photos')->get();
        return view('user.photos', compact('photos', 'albums'));
    }    

    public function showPhoto($id)
    {
        $photo = Photo::with('user')->findOrFail($id);
        $photo->increment('views');
        $randomPhotos = Photo::where('id', '!=', $id)
                             ->where('banned', false)
                             ->where('premium', false)
                             ->where('status', true)
                             ->inRandomOrder()
                             ->take(8)
                             ->get();
        $albums = Auth::check() ? Album::where('user_id', Auth::id())->with('photos')->get() : [];

        // Cek apakah foto dibanned
        if ($photo->banned) {
            return redirect()->route('home')->with('error', 'Foto ini telah dibanned.');
        }

        // Cek apakah foto bersifat premium
        if ($photo->premium) {
            // Cek apakah pengguna sudah berlangganan ke pengguna yang mengunggah foto
            if (!Auth::check() || (Auth::id() !== $photo->user_id && !Auth::user()->subscriptions()->where('target_user_id', $photo->user_id)->exists())) {
                return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke foto ini.');
            }
        }

        return view('photos.show', compact('photo', 'randomPhotos', 'albums'));
    }

    public function createphotos()
    {
        return view('photos.create');
    }

    public function editPhoto($id)
    {
        $photo = Photo::findOrFail($id);
        if (Auth::id() !== $photo->user_id) {
            return redirect()->route('user.profile')->with('error', 'Anda tidak memiliki izin untuk mengedit foto ini.');
        }
        return view('photos.edit', compact('photo'));
    }

    public function storePhoto(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg',
            'hashtags' => 'required|string',
            'premium' => 'required|boolean',
            'status' => 'required|in:1,0',
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
            'status' => $validated['status'],
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
            'status' => 'required|in:1,0',
        ]);

        $photo->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'hashtags' => json_encode(explode(',', $validated['hashtags'])),
            'status' => $validated['status'],
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
}