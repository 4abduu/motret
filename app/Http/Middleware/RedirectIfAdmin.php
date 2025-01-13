<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->view('admin.dashboard'); // Redirect ke halaman admin jika user adalah admin
        }

        return $next($request);
    }
}