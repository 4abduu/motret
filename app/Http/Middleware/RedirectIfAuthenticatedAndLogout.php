<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RedirectIfAuthenticatedAndLogout
{
    /**
     * Menangani status banned pada pengguna yang sedang login.
     *
     * Middleware ini memeriksa apakah pengguna yang sedang login dibanned. 
     * - Jika pengguna dibanned secara permanen, mereka akan logout dan diarahkan ke halaman login dengan pesan alasan banned.
     * - Jika pengguna dibanned sementara, sistem akan memeriksa apakah waktu banned telah berakhir. Jika sudah, status banned akan dihapus dan pengguna dapat melanjutkan sesi.
     * - Jika pengguna tidak dibanned, permintaan akan diteruskan ke proses berikutnya.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
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