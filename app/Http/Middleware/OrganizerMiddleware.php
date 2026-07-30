<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrganizerMiddleware
{
    /**
     * Hanya izinkan user dengan role 'organizer' yang organisasinya
     * sudah diverifikasi (status approved) oleh Superadmin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!Auth::check() || $user->role !== 'organizer') {
            return redirect()->route('organizer.login')
                             ->with('error', 'Akses ditolak. Silakan login sebagai Panitia/Organisasi.');
        }

        if (!$user->organization || !$user->organization->isApproved()) {
            Auth::logout();
            return redirect()->route('organizer.login')
                ->with('error', 'Akun organisasi Anda belum/tidak disetujui oleh Superadmin.');
        }

        return $next($request);
    }
}
