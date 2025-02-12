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
}