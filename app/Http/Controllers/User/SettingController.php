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
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Constructor untuk mengatur middleware.
     * Middleware `auth` diterapkan untuk semua fungsi dalam controller ini.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman pengaturan pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('user.settings');
    }
    
    /**
     * Memeriksa apakah username sudah digunakan.
     * Memvalidasi format username dan memeriksa keberadaannya di database.
     *
     * @param \Illuminate\Http\Request $request Data permintaan pengecekan username.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUsername(Request $request)
    {
        $username = trim($request->username);
        if (!preg_match('/^[a-z0-9._]+$/', $username)) {
            return response()->json(['exists' => true]); // Anggap username tidak valid sebagai sudah ada
        }
        Log::info('Checking username:', ['username' => $username]); // Log input username
        $exists = User::where('username', 'LIKE', $username)->exists();
        Log::info('Username exists:', ['exists' => $exists]); // Log hasil query
        return response()->json(['exists' => $exists]);
    }

    /**
     * Memeriksa apakah username yang dimasukkan sesuai dengan username pengguna yang sedang login.
     *
     * @param \Illuminate\Http\Request $request Data permintaan pengecekan username.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVerificationUsername(Request $request)
    {
        $username = $request->input('username');
        if (!preg_match('/^[a-z0-9._]+$/', $username)) {
            return response()->json(['isValid' => false]); // Anggap username tidak valid sebagai tidak sesuai
        }
        $isValid = $username === Auth::user()->username;
    
        return response()->json(['isValid' => $isValid]);
    }

    /**
     * Memeriksa apakah email sudah digunakan.
     * Memvalidasi format email dan memeriksa keberadaannya di database.
     *
     * @param \Illuminate\Http\Request $request Data permintaan pengecekan email.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Memperbarui username pengguna yang sedang login.
     *
     * Username baru harus unik, maksimal 20 karakter,
     * dan hanya boleh berisi huruf kecil, angka, titik, atau garis bawah.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUsername(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'string',
                'max:20',
                'unique:users,username,' . Auth::id(),
                'regex:/^[a-z0-9._]+$/', // Hanya huruf kecil, angka, titik, dan underscore
            ],
        ]);
    
        $user = Auth::user();
        $user->username = $validated['username'];
        $user->save();
    
        return redirect()->route('user.settings')->with('success', 'Username berhasil diperbarui.');
    }

    /**
     * Memperbarui password pengguna yang sedang login.
     *
     * Password baru harus memiliki minimal 8 karakter,
     * dan harus mengandung huruf dan angka.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);
    
        $user = Auth::user();
        if (Hash::check($request->current_password, $user->password)) {
            if (Hash::check($request->new_password, $user->password)) {
                return back()->withErrors(['new_password' => 'Password baru tidak boleh sama dengan password lama.']);
            }
    
            $user->password = Hash::make($validated['new_password']);
            $user->save();
            return redirect()->route('user.settings')->with('success', 'Password berhasil diperbarui.');
        } else {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }
    }

    /**
     * Memperbarui email pengguna yang sedang login.
     *
     * Email baru harus unik dan valid.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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
    
    /**
     * Mengirimkan pengajuan verifikasi akun oleh pengguna.
     *
     * Pengguna wajib mengisi nama lengkap, username yang sesuai dengan akun saat ini,
     * alasan pengajuan, serta mengunggah dokumen berupa KTP dan foto selfie.
     * Dokumen tambahan seperti portofolio dan sertifikat bersifat opsional.
     *
     * Jika pengajuan berhasil, data akan disimpan dan notifikasi akan dikirimkan ke pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitVerification(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:20',
                'regex:/^[a-z0-9._]+$/', // Hanya huruf kecil, angka, titik, dan underscore
            ],
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
