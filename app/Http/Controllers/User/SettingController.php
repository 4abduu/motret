<?php

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Models\VerificationDocument;
use App\Models\Notif;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        return view('user.settings');
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

    public function updateUsername(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:20|unique:users,username,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->username = $validated['username'];
        $user->save();

        return redirect()->route('user.settings')->with('success', 'Username berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($validated['new_password']);
            $user->save();
            return redirect()->route('user.settings')->with('success', 'Password berhasil diperbarui.');
        } else {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }
    }

    public function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'old_email' => 'required|string|email|max:255',
            'new_email' => 'required|string|email|max:255|unique:users,email',
            'verification_code' => 'required|string|size:8',
        ]);

        $user = Auth::user();
        if ($user->email !== $validated['old_email']) {
            return back()->withErrors(['old_email' => 'Email lama tidak sesuai.']);
        }

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $validated['old_email'])
            ->where('token', $validated['verification_code'])
            ->where('type', 'email')
            ->first();

        if (!$passwordReset || Carbon::parse($passwordReset->created_at)->lt(Carbon::now()->subMinutes(30))) {
            return back()->withErrors(['verification_code' => 'Kode verifikasi tidak valid atau telah kadaluarsa.']);
        }

        $user->email = $validated['new_email'];
        $user->save();

        DB::table('password_reset_tokens')->where('email', $validated['old_email'])->delete();

        return redirect()->route('user.settings')->with('success', 'Email berhasil diperbarui.');
    }

    // public function checkVerificationUsername(Request $request)
    // {
    //     $username = $request->input('username');
    //     $isValid = $username === Auth::user()->username;
    
    //     return response()->json(['isValid' => $isValid]);
    // }
    
    public function submitVerification(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'ktp' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'selfie' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            'portfolio' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'certificate' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'reason' => 'required|string|max:1000',
        ]);
    
        // Cek apakah username yang diinputkan sesuai dengan username yang ada di tabel users
        if ($validated['username'] !== Auth::user()->username) {
            return redirect()->back()->withErrors(['username' => 'Username tidak sesuai dengan username yang terdaftar di sistem.'])->withInput();
        }
    
        $verificationRequest = VerificationRequest::create([
            'user_id' => Auth::id(),
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);
    
        $documents = [
            'ktp' => $request->file('ktp'),
            'selfie' => $request->file('selfie'),
            'portfolio' => $request->file('portfolio'),
            'certificate' => $request->file('certificate'),
        ];
    
        foreach ($documents as $type => $file) {
            if ($file) {
                $path = $file->store('verifications/' . $type, 'public');
                VerificationDocument::create([
                    'verification_request_id' => $verificationRequest->id,
                    'file_path' => $path,
                    'file_type' => $type,
                ]);
            }
        }
    
        Notif::create([
            'notify_for' => Auth::id(),
            'notify_from' => null,
            'target_id' => Auth::id(),
            'type' => 'system',
            'message' => 'Pengajuan verifikasi Anda telah diterima dan sedang diproses.',
        ]);
    
        return redirect()->route('user.settings')->with('success', 'Pengajuan verifikasi telah dikirim.');
    }
}
