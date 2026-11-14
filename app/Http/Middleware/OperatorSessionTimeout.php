<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: OperatorSessionTimeout
 * Membatasi durasi waktu login Operator Lapangan maksimal hanya 8 jam.
 * Jika sudah mencapai atau melewati 8 jam sejak waktu login, sesi otomatis dihentikan.
 */
class OperatorSessionTimeout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user && $user->isOperator()) {
                $nowTimestamp = now()->timestamp;

                // Ambil waktu kadaluarsa sesi 8 jam dari session atau database user
                $sessionExpiresAt = session('operator_expires_at');
                $dbExpiresAt = ($user->login_expires_at && $user->login_expires_at->timestamp > $nowTimestamp)
                    ? $user->login_expires_at->timestamp
                    : null;

                $expiresAt = $sessionExpiresAt ?? $dbExpiresAt;

                // Sinkronkan jika salah satu kosong agar tidak ke-reset saat pindah halaman Blade
                if ($sessionExpiresAt && !$dbExpiresAt && $sessionExpiresAt > $nowTimestamp) {
                    $loginTimestamp = session('operator_login_at', $sessionExpiresAt - (User::OPERATOR_SESSION_HOURS * 3600));
                    $user->update([
                        'last_login_at' => \Carbon\Carbon::createFromTimestamp($loginTimestamp),
                        'login_expires_at' => \Carbon\Carbon::createFromTimestamp($sessionExpiresAt),
                    ]);
                } elseif (!$sessionExpiresAt && $dbExpiresAt) {
                    $loginTimestamp = $user->last_login_at
                        ? $user->last_login_at->timestamp
                        : ($dbExpiresAt - (User::OPERATOR_SESSION_HOURS * 3600));
                    session([
                        'operator_login_at' => $loginTimestamp,
                        'operator_expires_at' => $dbExpiresAt,
                    ]);
                }

                // Jika belum tercatat sama sekali di keduanya, inisialisasi batas 8 jam satu kali
                if (!$expiresAt) {
                    $loginAt = now();
                    $expiresCarbon = $loginAt->copy()->addHours(User::OPERATOR_SESSION_HOURS);
                    $expiresAt = $expiresCarbon->timestamp;

                    $user->update([
                        'last_login_at' => $loginAt,
                        'login_expires_at' => $expiresCarbon,
                    ]);

                    session([
                        'operator_login_at' => $loginAt->timestamp,
                        'operator_expires_at' => $expiresAt,
                    ]);
                }

                // Cek apakah durasi login 8 jam sudah habis
                if ($nowTimestamp >= $expiresAt) {
                    $user->update([
                        'login_expires_at' => null,
                        'remember_token' => null,
                    ]);

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    $message = 'Waktu login Operator Anda (maksimal 8 jam) telah berakhir. Silakan login kembali melalui peramban ponsel/perangkat Anda.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'expired' => true,
                            'message' => $message,
                            'redirect' => route('login'),
                        ], 401);
                    }

                    return redirect()->route('login')->withErrors([
                        'email' => $message,
                    ])->with('error', $message);
                }
            }
        }

        return $next($request);
    }
}

