<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User; // <-- Model utama yang kita kelola
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PhotographerController extends Controller
{
    /**
     * Menampilkan daftar semua fotografer (Tabel).
     */
    public function index()
    {
        $photographers = User::where('role', 'photographer')
                            ->with('photographerProfile.rates') 
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);

        return view('admin.photographers.index', compact('photographers'));
    }

    /**
     * Menampilkan form untuk membuat fotografer baru.
     */
    public function create()
    {
        return view('admin.photographers.create');
    }

    /**
     * Menyimpan fotografer baru ke database (User + Profile).
     */
    public function store(Request $request)
    {
        // 1. Validasi data gabungan dari form
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'bio' => 'nullable|string',
            'speciality' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
        ]);

        // 2. Buat data User
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make('12345678'), // Password default
            'role' => 'photographer',
            'status' => 'active', // Default status
        ]);

        // 3. Buat data Profile yang terhubung dengan User
        // Kita menggunakan relasi `photographerProfile()` yang ada di model User
        $user->photographerProfile()->create([
            'bio' => $validatedData['bio'] ?? null,
            'speciality' => $validatedData['speciality'] ?? null,
            'experience_years' => $validatedData['experience_years'] ?? 0,
        ]);

        return redirect()->route('admin.photographers.index')
                         ->with('success', 'Fotografer baru berhasil ditambahkan.');
    }

    
    /**
     * Menampilkan form untuk mengedit fotografer.
     * (UPDATED: Menambahkan ->load() untuk memastikan rates ada)
     */
    public function edit(User $photographer)
    {
        // PENTING: Gunakan 'load' untuk memastikan data dimuat ulang.
        // Jika data profile sudah ada di memori, kita harus memuat ulang rates-nya.
        
        // Perintah ini akan memuat ulang Profile, dan Rates yang ada di Profile.
        $photographer->load('photographerProfile.rates'); 

        return view('admin.photographers.edit', compact('photographer'));
    }

    /**
     * Memperbarui data fotografer di database (User + Profile).
     *
     * @param  \App\Models\User  $photographer
     */
    public function update(Request $request, User $photographer)
    {
        // 1. Validasi data gabungan
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($photographer->id), // Abaikan email user ini sendiri
            ],
            'bio' => 'nullable|string',
            'speciality' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
        ]);

        // 2. Update data User
        $photographer->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
        ]);

        // 3. Update data Profile (gunakan updateOrCreate untuk keamanan)
        // Ini akan meng-update profile jika ada, atau membuatnya jika
        // (karena suatu alasan) profile-nya terhapus.
        $photographer->photographerProfile()->updateOrCreate(
            ['photographer_id' => $photographer->id], // Kunci pencarian
            [
                // Data untuk di-update
                'bio' => $validatedData['bio'] ?? null,
                'speciality' => $validatedData['speciality'] ?? null,
                'experience_years' => $validatedData['experience_years'] ?? 0,
            ]
        );

        return redirect()->route('admin.photographers.index')
                         ->with('success', 'Data fotografer berhasil diperbarui.');
    }

    /**
     * Menghapus fotografer dari database.
     *
     * @param  \App\Models\User  $photographer
     */
    public function destroy(User $photographer)
    {
        // PENTING:
        // Asumsi Anda sudah mengikuti saran saya sebelumnya
        // untuk menambahkan event 'deleting' di model User.

        // Jika ya, Anda hanya perlu memanggil ini:
        $photographer->delete();

        // Event 'deleting' di model User akan otomatis
        // mencari dan menghapus 'photographer_profile' yang terkait.

        return redirect()->route('admin.photographers.index')
                         ->with('success', 'Fotografer berhasil dihapus.');
    }
}