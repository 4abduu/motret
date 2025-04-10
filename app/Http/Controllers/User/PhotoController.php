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
        if (Auth::check() && Auth::user()->role === 'admin') {
            abort(403, 'Admin tidak diizinkan mengakses halaman ini.');
        }
        
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

    // public function storePhoto(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'title' => 'required|string|max:255',
    //             'description' => 'required|string|max:255',
    //             'photo' => 'required|image|mimes:jpeg,png,jpg',
    //             'hashtags' => 'required|string',
    //             'premium' => 'boolean',
    //             'status' => 'in:1,0',
    //         ]);
    
    //             // Cek apakah foto dengan judul yang sama sudah diupload oleh user yang sama dalam waktu 5 menit terakhir
    //                 $recentPhoto = Photo::where('user_id', Auth::id())
    //                 ->where('title', $validated['title'])
    //                 ->where('created_at', '>=', now()->subMinutes(5))
    //                 ->first();
    
    //             if ($recentPhoto) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Anda sudah mengupload foto dengan judul yang sama dalam 5 menit terakhir.'
    //             ], 400);
    //             }
        
    //         $path = $request->file('photo')->store('photos', 'public');
        
    //         // Buat versi buram dari foto

        
    //         Photo::create([
    //             'user_id' => Auth::id(),
    //             'title' => $validated['title'],
    //             'description' => $validated['description'],
    //             'path' => $path,
    //             'hashtags' => json_encode(explode(',', $validated['hashtags'])),
    //             'premium' => $request->input('premium', false),
    //             'status' => $request->input('status', true),
    //         ]);
        
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Foto berhasil diupload.',
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal mengupload foto: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function storePhoto(Request $request)
{
    try {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Maksimal 5MB
            'hashtags' => 'required|string',
            'premium' => 'boolean',
            'status' => 'in:1,0',
        ]);

        // Cek duplikat judul dalam 5 menit terakhir
        $recentPhoto = Photo::where('user_id', Auth::id())
            ->where('title', $validated['title'])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($recentPhoto) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah upload foto ini sebelumnya!',
            ], 409);
        }

        $photoPath = $request->file('photo')->store('photos', 'public');

        $lowResDir = storage_path('app/public/low_res_photos');
        if (!file_exists($lowResDir)) {
            mkdir($lowResDir, 0775, true);
        }
        $lowResPath = $lowResDir . '/' . basename($photoPath);
        $image = Image::make(storage_path('app/public/' . $photoPath));
        $image->blur(50);
        $image->save($lowResPath);

        // Normalisasi hashtags jadi array JSON
        $hashtags = json_decode($validated['hashtags']);
        if (!is_array($hashtags)) {
            $hashtags = [$validated['hashtags']];
        }


        Photo::create([
            'user_id'     => Auth::id(),
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'photo'       => $photoPath,
            'hashtags'    => json_encode($hashtags),
            'premium'     => $validated['premium'] ?? false,
            'status'      => $validated['status'] ?? 1,
            'path'        => $photoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload!',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat upload foto.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}


    public function downloadPhoto(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);
        $user = Auth::user();
    
        // Ambil fingerprint dari cookie tanpa hashing
        $guestId = $request->cookie('guest_id') ?? null;
        $isGuest = !$user;
    
        if (!$guestId && $isGuest) {
            Log::error('Guest ID tidak ditemukan di cookie.', ['cookies' => $request->cookies->all()]);
            return back()->with('error', 'Fingerprint tidak ditemukan, silakan refresh halaman.');
        }
    
        Log::info('Guest ID diterima:', ['guest_id' => $guestId]);
    
        if ($user) {
            // === User Pro: Bisa download unlimited ===
            if ($user->role === 'pro') {
                // Cek apakah sudah ada record download untuk user dan foto ini
                $download = Download::where('user_id', $user->id)
                                    ->where('photo_id', $photo->id)
                                    ->first();
                
                if ($download) {
                    // Jika sudah ada, increment count_downloads
                    $download->increment('count_downloads');
                } else {
                    // Jika belum ada, buat record baru
                    Download::create([
                        'user_id' => $user->id,
                        'photo_id' => $photo->id,
                        'resolution' => 'original',
                        'count_downloads' => 1
                    ]);
                }
                
                return $this->processDownload($photo, 'original');
            }
            // === User Biasa: Terbatas 5x per minggu ===
            elseif ($user->role === 'user') {
                // Hitung total download user dalam 7 hari terakhir
                $weeklyDownloads = Download::where('user_id', $user->id)
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->sum('count_downloads');
                
                if ($weeklyDownloads >= 5) {
                    return back()->with('error', 'Anda telah mencapai batas download minggu ini.');
                }
                
                // Buat atau update record download untuk foto ini
                $download = Download::firstOrNew([
                    'user_id' => $user->id,
                    'photo_id' => $photo->id
                ]);
                
                $download->resolution = 'original';
                $download->count_downloads = $download->exists ? $download->count_downloads + 1 : 1;
                $download->save();
                
                return $this->processDownload($photo, 'original');
            }
        } 
        else {
    // === Guest Download: Mentok 5x, tidak reset ===
    $download = Download::where('guest_id', $guestId)
                        ->where('photo_id', $photo->id)
                        ->first();

    if (!$download) {
        Download::create([
            'guest_id' => $guestId,
            'photo_id' => $photo->id,
            'resolution' => 'low',
            'count_downloads' => 1
        ]);

        return $this->processDownload($photo, 'low'); // FIX: Download file langsung
    } elseif ($download->count_downloads < 5) {
        $download->increment('count_downloads');

        return $this->processDownload($photo, 'low'); // FIX: Download file langsung
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
            'premium' => 'boolean',
            'status' => 'in:1,0',
        ]);

        $photo->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'hashtags' => json_encode(explode(',', $validated['hashtags'])),
            'premium' => $request->input('premium', false),
            'status' => $request->input('status', true),
        ]);

        return redirect()->route('user.profile')->with('success', 'Foto berhasil diperbarui.');
    }

    public function destroyPhoto($id)
    {
        try{
            $photo = Photo::findOrFail($id);

            // Pastikan hanya pemilik foto yang bisa menghapus
            if (Auth::id() !== $photo->user_id) {
                return redirect()->route('user.profile')->with('error', 'Anda tidak memiliki izin untuk menghapus foto ini.');
            }
    
            // Hapus file asli dari storage
            $filePath = storage_path('app/public/' . $photo->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
    
            // Hapus file low-res dari storage
            $lowResPath = storage_path('app/public/low_res_photos/' . basename($photo->path));
            if (file_exists($lowResPath)) {
                unlink($lowResPath);
            }
    
            // Hapus data foto dari database
            $photo->delete();

            return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus.'], 200);
        }catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus foto: ' . $e->getMessage()
            ], 500);
        }
    }
}