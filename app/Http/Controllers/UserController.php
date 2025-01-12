<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\Download;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function profile()
    {
        return view('user.profile');
    }

    public function photos()
    {
        $photos = Photo::where('user_id', Auth::id())->get();
        return view('user.photos', compact('photos'));
    }

    public function createphotos()
    {
        return view('photos.create');
    }

    public function storePhoto(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hashtags' => 'nullable|string',
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        Photo::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'path' => $path,
            'hashtags' => json_encode(explode(',', $validated['hashtags'])),
            'status' => '1',
        ]);

        return redirect()->route('home')->with('success', 'Foto berhasil diunggah.');
    }

    public function showPhoto($id)
    {
        $photo = Photo::with('user')->findOrFail($id);
        return view('photos.show', compact('photo'));
    }

    public function downloadPhoto(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $user = Auth::user();
        $guestDownloadCount = session('guest_download_count', 0);

        // Cek role
        if ($user) {
            if ($user->role === 'pro') {
                return $this->processDownload($photo, 'original');
            } elseif ($user->role === 'user') {
                // Cek apakah perlu reset download count
                if ($user->download_reset_at === null || Carbon::now()->greaterThan($user->download_reset_at)) {
                    Log::info('User instance:', ['user' => $user]);
                    $user->download_reset_at = Carbon::now()->addWeek();
                    $user->save();
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
                return $this->processDownload($photo, 'low');
            } else {
                return back()->with('error', 'Anda telah mencapai batas download sebagai tamu.');
            }
        }
    }

    // Fungsi untuk proses download
    private function processDownload($photo, $resolution)
    {
        $filePath = storage_path('app/public/' . $photo->path);
        if ($resolution === 'low') {
            // Simpan versi buram gambar
            $lowResDir = storage_path('app/public/low_res_photos');
            if (!file_exists($lowResDir)) {
                mkdir($lowResDir, 0775, true);
            }
            $lowResPath = $lowResDir . '/' . basename($photo->path);
            if (!file_exists($lowResPath)) {
                $image = Image::make($filePath);
                $image->blur(50);
                $image->save($lowResPath);
            }
            return response()->download($lowResPath);
        }
        return response()->download($filePath);
    }
}