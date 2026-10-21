<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            ActivityLogger::logError('Gagal Login', "Email yang dicoba: {$request->email}");

            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $request->session()->regenerate();
        $user = Auth::user();

        ActivityLogger::log(
            'Login',
            "User {$user->name} (Role: {$user->role}) berhasil masuk ke sistem",
            $user->id
        );

        return $this->redirectByRole();
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'role' => 'peminjam',
        ]);

        ActivityLogger::log(
            'Registrasi',
            "Akun baru terdaftar: {$user->name} ({$user->email}, Role: peminjam)",
            $user->id
        );

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        ActivityLogger::log(
            'Logout',
            "User {$user->name} keluar dari sistem",
            $user->id
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    private function redirectByRole()
    {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'peminjam' => redirect()->route('peminjam.dashboard'),
            default => redirect('/'),
        };
    }
}
