<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{

    /**
     * Memeriksa apakah pengguna memiliki peran yang diizinkan untuk mengakses halaman.
     *
     * Middleware ini memeriksa apakah pengguna yang sedang login memiliki salah satu peran yang diberikan sebagai parameter.
     * - Jika pengguna tidak login, mereka akan diarahkan ke halaman login.
     * - Jika peran pengguna tidak sesuai dengan yang diizinkan, permintaan akan dibatalkan dengan respons 403 (Unauthorized).
     * - Jika peran pengguna sesuai, permintaan akan diteruskan ke proses berikutnya.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$roles Daftar peran yang diizinkan untuk mengakses halaman ini.
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}