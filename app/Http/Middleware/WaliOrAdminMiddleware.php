<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WaliOrAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (auth()->check()) {
            // Cek apakah user adalah Admin atau Wali Kelas
            if (auth()->user()->isAdmin() || auth()->user()->isWaliKelas()) {
                return $next($request);
            }
        }

        // Jika tidak memiliki akses, redirect ke halaman dashboard dengan pesan error
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman Rapor.');
    }
}