<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Menangani permintaan masuk.
     * 
     * Langkah-langkah:
     * 1. Memeriksa apakah pengguna sudah terautentikasi untuk setiap guard yang diberikan.
     * 2. Jika pengguna sudah terautentikasi:
     *    - Jika peran pengguna adalah "admin", diarahkan ke dashboard admin.
     *    - Jika peran pengguna adalah "user" atau "pro", diarahkan ke halaman utama.
     * 3. Jika pengguna belum terautentikasi, permintaan diteruskan ke middleware berikutnya.
     *
     * @param \Illuminate\Http\Request $request Permintaan HTTP yang masuk.
     * @param \Closure $next Middleware berikutnya.
     * @param string ...$guards Daftar guard yang akan diperiksa.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'user' || $user->role === 'pro') {
                    return redirect()->route('home');
                }
            }
        }

        return $next($request);
    }
}