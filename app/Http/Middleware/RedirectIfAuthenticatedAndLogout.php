<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RedirectIfAuthenticatedAndLogout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->banned) {
                $bannedUntil = $user->banned_until;
                $bannedType = $user->banned_type;

                // Periksa apakah waktu banned telah berakhir
                if ($bannedType === 'temporary' && Carbon::now()->greaterThanOrEqualTo($bannedUntil)) {
                    // Hapus status banned
                    $user->banned = false;
                    $user->banned_until = null;
                    $user->banned_reason = null;
                    $user->banned_type = null;
                    $user->save();
                } else {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    if ($bannedType === 'permanent') {
                        $message = 'Maaf, akun Anda telah dibanned permanen. Alasan: ' . $user->banned_reason;
                    } else {
                        $message = 'Akun Anda telah dibanned hingga ' . $bannedUntil->format('d-m-Y') . '. Alasan: ' . $user->banned_reason;
                    }

                    return redirect()->route('login')->with('status', $message);
                }
            }
        }

        return $next($request);
    }
}