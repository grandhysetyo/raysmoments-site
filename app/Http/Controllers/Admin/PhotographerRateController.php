<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhotographerRate;
use App\Models\PhotographerProfile; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class PhotographerRateController extends Controller
{
    /**
     * Menyimpan tarif baru untuk fotografer tertentu.
     */
    public function store(Request $request)
    {
        // 1. Validasi input: Mengubah nama field yang diterima dari view
        $validatedData = $request->validate([
            // Sekarang menerima ID dari tabel photographer_profiles.id
            'photographer_profile_id' => 'required|exists:photographer_profiles,id', 
            'city' => 'required|string|max:255',
            'base_rate' => 'required|numeric|min:0',
            'transport_fee' => 'nullable|numeric|min:0',
            'effective_start' => 'nullable|date',
            'effective_end' => 'nullable|date|after_or_equal:effective_start',
            'notes' => 'nullable|string',
        ]);
        
        $effectiveStart = $validatedData['effective_start'] ?? Carbon::now();
        $effectiveEnd = $validatedData['effective_end'] ?? null;
        
        // 2. Buat tarif baru
        PhotographerRate::create([
            // GUNAKAN ID PROFIL, TAPI SIMPAN KE KOLOM 'photographer_id' DI TABEL RATES
            'photographer_id' => $validatedData['photographer_profile_id'], 
            'city' => $validatedData['city'],
            'base_rate' => $validatedData['base_rate'],
            'transport_fee' => $validatedData['transport_fee'] ?? 0,
            'effective_start' => $effectiveStart,
            'effective_end' => $effectiveEnd,
            'notes' => $validatedData['notes'] ?? null,
        ]);

        // 3. Mendapatkan Model User untuk Redirect
        // Cari Profile berdasarkan ID Profil (yang baru saja disimpan), lalu ambil User-nya
        $user = PhotographerProfile::findOrFail($validatedData['photographer_profile_id']) 
                                   ->user; 
        
        return redirect()->route('admin.photographers.edit', $user) 
                         ->with('rate_success', 'Tarif baru berhasil ditambahkan untuk ' . $validatedData['city'] . '.');
    }

    public function destroy(PhotographerRate $rate)
    {
        // ... (Fungsi ini harus tetap berfungsi karena menggunakan relasi $rate->profile->user) ...
        $user = $rate->profile->user;
        $rate->delete();
        return redirect()->route('admin.photographers.edit', $user)
                         ->with('rate_success', 'Tarif berhasil dihapus.');
    }
}