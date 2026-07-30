<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Self-registration untuk Organisasi/Kepanitiaan (HIMA) baru.
 * Akun akan berstatus 'pending' sampai diverifikasi oleh Superadmin.
 */
class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('organizer.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'organization_name' => 'required|string|max:255',
            'organization_email' => 'required|email|max:255|unique:users,email',
            'organization_phone' => 'nullable|string|max:20',
            'organization_description' => 'nullable|string',
            'pic_name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $slug = Str::slug($data['organization_name']);
        $originalSlug = $slug;
        $i = 1;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i++;
        }

        $organization = Organization::create([
            'name' => $data['organization_name'],
            'slug' => $slug,
            'email' => $data['organization_email'],
            'phone' => $data['organization_phone'] ?? null,
            'description' => $data['organization_description'] ?? null,
            'status' => 'pending',
        ]);

        User::create([
            'name' => $data['pic_name'],
            'email' => $data['organization_email'],
            'password' => bcrypt($data['password']),
            'role' => 'organizer',
            'organization_id' => $organization->id,
        ]);

        return redirect()->route('organizer.login')->with(
            'success',
            'Pendaftaran organisasi berhasil dikirim! Akun Anda akan aktif setelah diverifikasi oleh Superadmin.'
        );
    }
}
