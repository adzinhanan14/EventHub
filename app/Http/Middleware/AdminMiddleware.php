<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Hanya izinkan Superadmin (role='superadmin') yang bisa lewat.
     * Superadmin bertugas mengawasi kelayakan seluruh penyelenggara (Organisasi/HIMA).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->role !== 'superadmin') {
            return redirect()->route('admin.login')
                             ->with('error', 'Akses ditolak. Silakan login sebagai Superadmin.');
        }

        return $next($request);
    }
}
