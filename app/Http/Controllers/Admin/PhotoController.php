<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;
class PhotoController extends Controller
{
    public function index()
    {
        $photos = Photo::all();
        return view('admin.managePhotos', compact('photos'));
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
            return response()->json(['success' => true, 'message' => 'Foto berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal memperbarui foto.']);
        }
    }

    public function deletePhoto($id)
    {
        try {
            Photo::findOrFail($id)->delete();
            return response()->json(['success' => true, 'message' => 'Foto berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Gagal menghapus foto.']);
        }
    }

public function banPhoto(Request $request, $id)
{
    try {
        $photo = Photo::findOrFail($id);

        if ($photo->banned) {
            return response()->json(['message' => 'Postingan ini telah dibanned.'], 400);
        }

        $report = Report::where('photo_id', $id)->first();
        if (!$report) {
            return response()->json(['message' => 'Laporan tidak ditemukan.'], 404);
        }

        $photo->banned = true;
        $photo->ban_expires_at = Carbon::now()->addDays(7);
        $photo->save();

        Report::where('photo_id', $id)->update(['status' => true]);

        return response()->json(['message' => 'Foto berhasil dibanned.']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Foto gagal dibanned: ' . $e->getMessage()], 500);
    }
}
}