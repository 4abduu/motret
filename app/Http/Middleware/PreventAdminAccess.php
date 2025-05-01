<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PreventAdminAccess
{

    /**
     * Mencegah pengguna dengan peran admin mengakses halaman tertentu.
     *
     * Jika pengguna yang sedang login memiliki peran 'admin',
     * maka permintaan akan dibatalkan dengan respons 403 Forbidden.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            abort(403, 'Admin tidak diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }
}