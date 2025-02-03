<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;  // Mengganti nama alias
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function login(Request $request)
    {
        $messages = [
            'email.required' => 'Email atau Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ];

        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ], $messages);

        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$loginType => $request->email, 'password' => $request->password];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('home');
            }
        }

        return back()->withErrors(['email' => 'Email/Username atau password salah.']);
    }

    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.min' => 'Username minimal harus 4 karakter.',
            'username.max' => 'Username maksimal 20 karakter.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
            'profile_photo.image' => 'Foto profil harus berupa gambar.',
            'profile_photo.mimes' => 'Foto profil harus berformat jpeg, png, atau jpg.',
        ];

        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->storeAs(
                'public/photo_profile',
                Str::random(40) . '.' . $request->file('profile_photo')->getClientOriginalExtension()
            );
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|unique:users,username|min:4|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],  // Menggunakan alias PasswordRule
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg',
        ], $messages);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'profile_photo' => $profilePhotoPath ? basename($profilePhotoPath) : null,
        ]);

        Auth::login($user);
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    
    public function guest()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();
    
            if ($user) {
                // Jika user sudah ada, login
                Auth::login($user);
            } else {
                // Buat username dari nama depan email (sebelum @)
                $username = explode('@', $googleUser->getEmail())[0];
    
                // Jika username sudah ada, tambah angka increment
                $originalUsername = $username;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }
    
                // Buat user baru
                $user = User::create([
                    'username' => $username, 
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt('password123'), 
                    'role' => 'user', 
                ]);
    
                // Login user baru
                Auth::login($user);
            }
    
            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong, please try again.');
        }
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }
    
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email_or_username' => 'required',
        ]);
    
        $user = User::where('email', $request->email_or_username)
                    ->orWhere('username', $request->email_or_username)
                    ->first();
    
        if (!$user) {
            return back()->withErrors(['email_or_username' => 'Email atau username tidak ditemukan.']);
        }
    
        // Generate 8 digit token angka
        $token = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email], // Cek apakah email sudah ada di tabel
            [
                'token' => $token,
                'created_at' => Carbon::now('UTC'),
            ]
        );
    
        Mail::to($user->email)->send(new PasswordResetMail($token));
    
        return redirect()->route('password.reset')->with('status', 'Kode verifikasi telah dikirim ke email Anda.');
    }
    
    public function showResetPasswordForm(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|digits:8',
            'password' => 'required|confirmed|min:8',
        ]);
    
        $passwordReset = DB::table('password_reset_tokens')
                            ->where('token', $request->token)
                            ->first();
    
        if (!$passwordReset || Carbon::parse($passwordReset->created_at)->lt(Carbon::now('UTC')->subMinutes(30))) {
            return back()->withErrors(['token' => 'Token tidak valid atau telah kadaluarsa.']);
        }
    
        $user = User::where('email', $passwordReset->email)->first();
    
        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }
    
        $user->password = Hash::make($request->password);
        $user->save();
    
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
    
        return redirect()->route('login')->with('status', 'Password berhasil diubah.');
    }
    
}