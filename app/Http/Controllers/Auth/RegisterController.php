<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Menampilkan halaman register.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Memproses permintaan register.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Buat user baru
        // PERHATIKAN: Peran 'owner' di-hardcode.
        // Ini berbahaya jika dibiarkan publik.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'owner', // Hardcode peran sebagai 'owner'
            'status' => 'active',
        ]);

        // 3. Kirim event (opsional tapi bagus)
        event(new Registered($user));

        // 4. Loginkan user yang baru daftar
        Auth::login($user);

        // 5. Redirect ke dashboard admin
        return redirect(route('admin.dashboard'));
    }
}