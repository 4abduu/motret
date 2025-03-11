<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Photo;
use App\Models\Album;
use App\Models\User;
use App\Models\SubscriptionPriceUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['showProfile']);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Hanya ambil foto dan album milik user sendiri
        $photos = Photo::where('user_id', $user->id)
                        ->where('premium', false)
                        ->get();
        
        $premiumPhotos = Photo::where('user_id', $user->id)
                              ->where('premium', true)
                              ->get();
        
        $albums = Album::where('user_id', $user->id)
                       ->with(['photos' => function ($query) {
                           $query->where('status', 1); // Ambil hanya foto publik dalam album
                       }])
                       ->get();
        
        $hasSubscriptionPrice = SubscriptionPriceUser::where('user_id', $user->id)->exists();
        
        return view('user.profile', compact('user', 'photos', 'premiumPhotos', 'albums', 'hasSubscriptionPrice'));
    }
    
    public function showProfile($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $isOwner = Auth::check() && Auth::user()->id == $user->id;
        
        // Jika pemilik akun yang buka, tampilkan semua foto
        if ($isOwner) {
            $photos = Photo::where('user_id', $user->id)->where('premium', false)->get();
            $premiumPhotos = Photo::where('user_id', $user->id)->where('premium', true)->get();
            $albums = Album::where('user_id', $user->id)->with('photos')->get();
        } else {
            // Jika bukan pemilik akun, hanya tampilkan yang publik
            $photos = Photo::where('user_id', $user->id)->where('premium', false)->where('status', 1)->get();
            $premiumPhotos = Photo::where('user_id', $user->id)->where('premium', true)->where('status', 1)->get();
            $albums = Album::where('user_id', $user->id)->where('status', 1)->with(['photos' => function ($query) {
                $query->where('status', 1);
            }])->get();
        }
        
        $isSubscribed = Auth::check() && Auth::user()->subscriptions()->where('target_user_id', $user->id)->exists();
        $hasSubscriptionPrice = SubscriptionPriceUser::where('user_id', $user->id)->exists();
        
        return view('user.profile', compact('user', 'photos', 'premiumPhotos', 'albums', 'isSubscribed', 'hasSubscriptionPrice'));
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
}