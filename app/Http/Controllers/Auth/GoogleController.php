<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * Login Instan via Sosial Media (SSO) menggunakan Laravel Socialite.
 * Dipakai pembeli tiket ("Continue with Google") maupun Admin Organisasi (Panitia/HIMA)
 * yang emailnya sudah terdaftar sebagai pemilik organisasi.
 */
class GoogleController extends Controller
{
    public function redirect(Request $request)
    {
        // Simpan tujuan setelah login (misal kembali ke halaman checkout)
        if ($request->query('redirect')) {
            session(['sso_intended' => $request->query('redirect')]);
        }

        return Socialite::driver('google')
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            Log::error('Google SSO gagal: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Login dengan Google gagal, silakan coba lagi.');
        }

        // Cari user berdasarkan provider_id ATAU email yang sudah pernah terdaftar
        $user = User::where('provider', 'google')->where('provider_id', $googleUser->getId())->first();

        if (!$user) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            // Tautkan akun lama (mis. dibuat manual oleh Superadmin sebagai organizer) ke Google
            $user->forceFill([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ])->save();
        } else {
            // Pengguna baru: langsung dibuatkan akun customer, tanpa form registrasi.
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(Str::random(32)),
                'role' => 'user',
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user, remember: true);
        session()->regenerate();

        // Arahkan sesuai peran
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isOrganizer()) {
            return redirect()->route('organizer.dashboard');
        }

        $intended = session()->pull('sso_intended');
        return redirect($intended ?: route('home'))->with('success', 'Berhasil masuk dengan Google.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil keluar.');
    }
}
