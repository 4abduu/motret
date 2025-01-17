<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

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

    // public function login(Request $request)
    // {
    //     $messages = [
    //         'email.required' => 'Email harus diisi.',
    //         'email.email' => 'Format email tidak valid.',
    //         'password.required' => 'Password harus diisi.',
    //     ];

    //     $validated = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ], $messages);

    //     $credentials = $request->only('email', 'password');
        
    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    //         if ($user->role === 'admin') {
    //             return redirect()->route('admin.dashboard');
    //         } else {
    //             return redirect()->route('home');
    //         }
    //     }

    //     return back()->withErrors(['email' => 'Email atau password salah.']);
    // }

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
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
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
}
