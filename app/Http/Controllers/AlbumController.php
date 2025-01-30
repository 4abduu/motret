<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    public function show($id)
    {
        $album = Album::with('photos')->findOrFail($id);
        return view('albums.show', compact('album'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
    
        $album = Album::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
        ]);
    
        return redirect()->route('user.profile')->with('success', 'Album berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);

        $album->name = $request->name;
        $album->description = $request->description;

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