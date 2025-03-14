<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id)
    {
        if (Auth::user()->role === 'admin') {
            abort(403, 'Admin tidak diizinkan mengakses halaman ini.');
        }
        $album = Album::with(['photos' => function($query) {
            $query->where('banned', false)
            ->where('premium', false);
        }])->findOrFail($id);
        return view('albums.show', compact('album'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'required|in:0,1',
        ]);
    
        // Jika validasi gagal, kembalikan error dalam format JSON untuk AJAX, atau redirect untuk non-AJAX
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
    
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        try {
            // Simpan album ke database
            $album = Album::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status
            ]);
    
            // Jika request berasal dari AJAX, kembalikan JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Album berhasil dibuat!',
                    'album' => $album
                ]);
            }
    
            // Jika bukan AJAX, redirect ke halaman album
            return redirect()->route('user.profile')->with('success', 'Album berhasil dibuat.');
    
        } catch (\Exception $e) {
            // Tangani error
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat membuat album.',
                    'error' => $e->getMessage()
                ], 500);
            }
    
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat album.');
        }
    }
    
    

    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);

        $album->name = $request->name;
        $album->description = $request->description;
        $album->status = $request->status;

        $album->save();

        return redirect()->back()->with('success', 'Album berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();

        return redirect()->back()->with('success', 'Album berhasil dihapus.');
    }

    public function removePhoto($albumId, $photoId)
    {
        $album = Album::findOrFail($albumId);
        $album->photos()->detach($photoId);

        // Mengarahkan kembali dengan informasi sukses
        return redirect()->route('albums.show', $albumId)->with('success', 'Foto berhasil dihapus dari album.');
    }

    public function addPhoto($albumId, $photoId)
    {
        $album = Album::findOrFail($albumId);
        $photo = Photo::findOrFail($photoId);

        // Pastikan pengguna memiliki album ini
        if ($album->user_id !== Auth::id()) {
            return redirect()->route('albums.show', $albumId)->with('error', 'Anda tidak memiliki album ini.');
        }

        // Cek apakah foto sudah ada di album
        if ($album->photos()->where('photo_id', $photoId)->exists()) {
            return redirect()->route('albums.show', $albumId)->with('warning', 'Foto sudah ada di album.');
        }

        // Tambahkan foto ke album
        $album->photos()->attach($photoId);

        return redirect()->route('albums.show', $albumId)->with('success', 'Foto berhasil ditambahkan ke album.');
    }

    public function updateTitle(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $album = Album::findOrFail($id);
        $album->name = $request->title;
        $album->save();

        return redirect()->route('albums.show', $id)->with('success', 'Judul album berhasil diperbarui.');
    }

    public function updateDescription(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
        ]);

        $album = Album::findOrFail($id);
        $album->description = $request->description;
        $album->save();

        return redirect()->route('albums.show', $id)->with('success', 'Deskripsi album berhasil diperbarui.');
    }
}