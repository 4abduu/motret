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
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function index()
    {
        //
    }

    // menampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }
    // menampilkan halaman daftar
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showAuthGoogle()
    {
        return view('auth.google');
    }
    // redirect ke google login
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->with(['prompt' => 'select_account'])->redirect();
    }
    // fungsi untuk menghandle login
    public function login(Request $request)
    {
        Log::info('Login attempt', ['request' => $request->only('email')]);
    
        $messages = [
            'email.required' => 'Email atau Username harus diisi.',
            'password.required' => 'Password harus diisi.',
        ];
    
        $validated = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ], $messages);
    
        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        Log::info('Login type detected', ['type' => $loginType]);
    
        $credentials = [$loginType => $request->email, 'password' => $request->password];
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            Log::info('Login successful', ['user_id' => $user->id, 'role' => $user->role]);
    
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('login_success', 'Login berhasil! Selamat datang admin.');
            } else {
                return redirect()->route('home')->with('login_success', 'Login berhasil! Selamat datang, ' . $user->username);
            }
        }
    
        Log::warning('Login failed', ['email/username' => $request->email]);
        return back()
        ->withInput($request->only('email'))
        ->withErrors(['email' => 'Email/Username atau password salah.']); 
    }
    // fungsi untuk menghandle daftar
    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'Nama harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.min' => 'Username minimal harus 4 karakter.',
            'username.max' => 'Username maksimal 20 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf kecil, angka, titik, dan underscore.',
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
        ];
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'unique:users,username',
                'min:4',
                'max:20',
                'regex:/^[a-z0-9._]+$/', // Hanya huruf kecil, angka, titik, dan underscore
            ],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], $messages);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);
        
            Auth::login($user);
            return redirect()->route('login')->with('register_success', 'Register berhasil! Silahkan login.');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['general' => 'Registrasi gagal. Silakan cek data Anda.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('logout_success', 'Berhasil logout');

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
                return view('auth.google', compact('googleUser'));
            }
    
            return redirect()->route('home')->with('login_success', 'Login berhasil! Selamat datang, ' . $user->username);
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
                'type' => 'password',
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

    public function sendEmailVerification(Request $request)
    {
        $validated = $request->validate([
            'old_email' => 'required|string|email|max:255',
        ]);
    
        $user = Auth::user();
        if ($user->email !== $validated['old_email']) {
            return response()->json(['success' => false, 'message' => 'Email tidak sesuai.']);
        }
    
        // Generate 8 digit token angka
        $token = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $validated['old_email']], // Cek apakah email sudah ada di tabel
            [
                'token' => $token,
                'type' => 'email',
                'created_at' => Carbon::now('UTC'),
            ]
        );
    
        Mail::to($validated['old_email'])->send(new EmailVerificationMail($token));
    
        return response()->json(['success' => true, 'message' => 'Kode verifikasi telah dikirim ke email Anda.']);
    }
    public function verifyEmailCode(Request $request)
    {
        $validated = $request->validate([
            'verification_code' => 'required|digits:8',
            'new_email' => 'required|string|email|max:255|unique:users,email',
        ]);

        $user = Auth::user();
        $emailVerification = DB::table('password_reset_tokens')
                                ->where('email', $user->email)
                                ->where('token', $request->verification_code)
                                ->first();

        if (!$emailVerification || Carbon::parse($emailVerification->created_at)->lt(Carbon::now('UTC')->subMinutes(30))) {
            return back()->withErrors(['verification_code' => 'Kode verifikasi tidak valid atau telah kadaluarsa.']);
        }

        $user->email = $validated['new_email'];
        $user->save();

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->route('user.settings')->with('success', 'Email berhasil diubah.');
    }

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
}