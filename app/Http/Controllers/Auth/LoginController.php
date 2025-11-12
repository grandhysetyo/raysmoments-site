<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Memproses permintaan login.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Coba autentikasi
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // 3. Jika gagal, kembalikan dengan error
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ])->redirectTo(route('login'));
        }

        // 4. Jika berhasil, regenerate session
        $request->session()->regenerate();

        // 5. Dapatkan data user
        $user = Auth::user();
        
        // 6. Redirect berdasarkan peran
        if ($user->role === 'photographer') {
            return redirect()->intended(route('photographer.dashboard'));
        }

        if (in_array($user->role, ['admin'])) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Fallback jika peran tidak dikenal
        Auth::logout();
        return redirect('/')->with('error', 'Akun Anda tidak memiliki peran yang valid.');
    }

    /**
     * Memproses permintaan logout.
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}