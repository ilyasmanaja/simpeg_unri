<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // --- LOGIKA REDIRECT BERBASIS ROLE ---
            $user = Auth::user();
            $role = strtolower($user->roles()->pluck('jenis_role')->first() ?? 'dosen'); // Menggunakan accessor yang sudah kamu buat

            if ($role === 'operator') {
                return redirect()->intended('/operator/data-diri');
            } elseif ($role === 'pimpinan') {
                return redirect()->intended('/pimpinan/dashboard');
            }

            // Default untuk Dosen/Tendik
            return redirect()->intended('/dosen/data-diri');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}