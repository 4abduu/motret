<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdmin
{

    /**
     * Mengarahkan pengguna dengan peran 'admin' ke dashboard admin.
     *
     * Jika pengguna yang sedang login memiliki peran 'admin',
     * mereka akan diarahkan ke halaman dashboard admin.
     * Pengguna non-admin dapat melanjutkan ke halaman yang diminta.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard'); // Redirect ke halaman admin jika user adalah admin
        }

        return $next($request);
    }
}