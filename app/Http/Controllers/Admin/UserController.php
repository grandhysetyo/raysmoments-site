<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PhotographerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // <-- Penting untuk Hashing
use Illuminate\Support\Facades\Auth; // <-- Penting untuk cek user
use Illuminate\Validation\Rule;      // <-- Penting untuk validasi update

class UserController extends Controller
{
    /**
     * Menampilkan daftar user (Tabel).
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat user baru.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => ['required', Rule::in(['admin', 'owner'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // === KEBUTUHAN KHUSUS: SET PASSWORD OTOMATIS ===
        $validatedData['password'] = Hash::make('12345678');
        // =============================================

        User::create($validatedData);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui user di database.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            // Validasi email unik, tapi abaikan ID user ini sendiri
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'owner'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        // Catatan: Kita tidak memperbarui password di sini.
        // Itu harus menjadi fitur terpisah (misal: "Reset Password").

        $user->update($validatedData);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        // Fitur keamanan: Jangan biarkan admin menghapus akunnya sendiri
        if (Auth::id() == $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }
        
        // Fitur keamanan: Jangan biarkan user terakhir dihapus (opsional tapi bagus)
        if (User::count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus user terakhir.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil dihapus.');
    }
}