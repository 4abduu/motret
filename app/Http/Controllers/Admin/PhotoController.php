<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;
class PhotoController extends Controller
{
    /**
     * Menginisialisasi middleware untuk mengautentikasi admin.
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan halaman utama manajemen foto.
     * Mengambil semua foto dari database.
     * Diurutkan dengan descending.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $photos = Photo::orderBy('created_at', 'desc')->get();
        return view('admin.managePhotos', compact('photos'));
    }

    /**
     * Memperbarui informasi foto tertentu.
     * Memvalidasi input dan memperbarui data foto di database.
     *
     * @param int $id ID foto.
     * @param \Illuminate\Http\Request $request Data permintaan yang berisi informasi foto.
     * @return \Illuminate\Http\JsonResponse
    */
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

    /**
     * Menghapus foto tertentu.
     * Menghapus foto dari database berdasarkan ID serta menghapus dari file storage.
     *
     * @param int $id ID foto.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePhoto($id)
    {
        try {
            $photo = Photo::findOrFail($id);
    
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
    
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus foto: ' . $e->getMessage()
            ]);
        }
    }    

    /**
     * Membanned foto tertentu.
     * Menandai foto sebagai dibanned dan memperbarui status laporan terkait.
     *
     * @param \Illuminate\Http\Request $request Data permintaan.
     * @param int $id ID foto.
     * @return \Illuminate\Http\JsonResponse
     */
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