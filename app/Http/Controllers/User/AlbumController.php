<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AlbumController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index($id)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'sometimes|in:0,1',
        ]);

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
            $albumData = [
                'user_id' => Auth::id(),
                'name' => $request->name,
                'description' => $request->description,
                'status' => Auth::user()->role === 'pro' ? ($request->status ?? 1) : 1
            ];
    
            $album = Album::create($albumData);
    
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Album berhasil dibuat',
                    'album' => $album,
                    'current_user_id' => Auth::id(),
                    'current_user_role' => Auth::user()->role
                ]);
            }
    
            return redirect()->route('user.profile')->with('success', 'Album berhasil dibuat');
    
        } catch (\Exception $e) {
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
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => 'sometimes|in:0,1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        try {
            $album->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status ?? $album->status
            ]);
        
            return response()->json([
                'success' => true,
                'album' => $album,
                'current_user_role' => Auth::user()->role
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating album',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $album = Album::findOrFail($id);
        return response()->json([
            'success' => true,
            'album' => $album,
            'current_user_role' => Auth::user()->role
        ]);
    }

    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        if ($album->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $album->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Album berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus album.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addPhoto($albumId, $photoId)
    {
        $album = Album::findOrFail($albumId);
        $photo = Photo::findOrFail($photoId);

        if ($album->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke album ini'
            ], 403);
        }

        if ($album->photos()->where('photo_id', $photoId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Foto sudah ada di album'
            ], 400);
        }

        $album->photos()->attach($photoId);

        return response()->json(['success' => true]);
    }

    public function removePhoto($albumId, $photoId)
    {
        $album = Album::findOrFail($albumId);
        
        if ($album->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke album ini'
            ], 403);
        }

        $album->photos()->detach($photoId);

        return response()->json(['success' => true]);
    }

    public function updateTitle(Request $request, $id)
    {
        $request->validate([
            'title' => 'required_without:name|string|max:255',
            'name' => 'required_without:title|string|max:255',
        ]);
    
        $album = Album::findOrFail($id);
        
        if ($album->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke album ini'
            ], 403);
        }
    
        $album->name = $request->title ?? $request->name;
        $album->save();
    
        return response()->json([
            'success' => true,
            'title' => $album->name
        ]);
    }

    public function updateDescription(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
        ]);
    
        $album = Album::findOrFail($id);
        
        Log::debug('Updating album description', [
            'old_description' => $album->description,
            'new_description' => $request->description
        ]);
    
        if ($album->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
    
        // Clean and update description
        $cleanDescription = trim(strip_tags($request->input('description')));
        $album->description = $cleanDescription;
        $album->save();
    
        return response()->json([
            'success' => true,
            'description' => $cleanDescription
        ]);
    }  
    

    public function updateVisibility(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:0,1',
        ]);        

        // Find the album
        $album = Album::findOrFail($id);

        // Check authorization
        if ($album->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Update the status
        try {
            $album->status = (string) $request->status;
            $album->save();

            return response()->json([
                'success' => true,
                'status' => $album->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating visibility',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}