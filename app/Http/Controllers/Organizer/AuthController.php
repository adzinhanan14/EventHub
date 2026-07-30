<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'organizer') {
            return redirect()->route('organizer.dashboard');
        }
        return view('organizer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'organizer') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini bukan akun Panitia/Organisasi.']);
            }

            if (!$user->organization) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun ini belum terhubung ke organisasi manapun.']);
            }

            if ($user->organization->status === 'pending') {
                Auth::logout();
                return back()->withErrors(['email' => 'Organisasi Anda masih menunggu verifikasi Superadmin.']);
            }

            if ($user->organization->status === 'rejected') {
                Auth::logout();
                return back()->withErrors(['email' => 'Pendaftaran organisasi Anda ditolak Superadmin. Alasan: ' . ($user->organization->rejection_reason ?? '-')]);
            }

            $request->session()->regenerate();
            return redirect()->route('organizer.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda berikan tidak terdaftar di database kami.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
