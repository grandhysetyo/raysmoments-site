<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package; 
use App\Models\Payment; 
use App\Models\Addon;
use App\Models\User;
use App\Models\ClientDetail;
use App\Models\BookingAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB; 

class AdminBookingController extends Controller
{
    /**
     * Menampilkan form untuk membuat pesanan baru (Oleh Admin).
     */
    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();

        return view('admin.bookings.create', compact('packages', 'addons'));
    }

    /**
     * Menyimpan pesanan baru ke database (Oleh Admin).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // --- Data User & Client Details ---
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email', 
            'whatsapp_number' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'faculty_or_major' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            
            // --- Data Booking & Payment ---
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date|after_or_equal:today',
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            'notes' => 'nullable|string',
            
            // --- Kalkulasi Harga ---
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id', // Memastikan ID addon valid
            'grand_total' => 'required|numeric|min:0', // Grand Total dari JS
            'dp_amount' => 'required|numeric|min:0|max:' . $request->input('grand_total'), // DP dari JS
        ]);

        // 1. RE-KALKULASI HARGA DI SISI SERVER (Security Check)
        $package = Package::findOrFail($validatedData['package_id']);
        $selectedAddons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
        
        $packagePrice = $package->price;
        $addonsTotal = $selectedAddons->sum('price');
        $grandTotalFinal = $packagePrice + $addonsTotal;
        $dpAmountFinal = $grandTotalFinal * 0.5;
        
        // Final Security Check (SAMA)
        if (floatval($validatedData['grand_total']) != $grandTotalFinal || floatval($validatedData['dp_amount']) != $dpAmountFinal) {
             return back()->with('error', 'Terjadi ketidaksesuaian harga. Harap hitung ulang dan pastikan harga paket/addon sudah benar.')->withInput();
        }

        DB::beginTransaction();

        try {
            /// A. BUAT USER BARU (ROLE: client)
            $user = User::create([
                'name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
                'password' => Hash::make('12345678'), // Password default klien
                'role' => 'client',
                'status' => 'active',
            ]);

            // B. BUAT DETAIL KLIEN
            $user->clientDetails()->create([
                'full_name' => $validatedData['full_name'],
                'whatsapp_number' => $validatedData['whatsapp_number'],
                'university' => $validatedData['university'],
                'faculty_or_major' => $validatedData['faculty_or_major'],
                'instagram' => $validatedData['instagram'],
            ]);

            // C. BUAT BOOKING
            $booking = Booking::create([
                'order_code' => 'CLI-' . time(), 
                'client_id' => $user->id, 
                'package_id' => $validatedData['package_id'],
                
                // --- HARGA FINAL DARI SERVER ---
                'package_price' => $packagePrice,
                'addons_total' => $addonsTotal,
                'grand_total' => $grandTotalFinal, 
                
                // --- DATA NON-HARGA ---
                'event_date' => $validatedData['event_date'],
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'notes' => $validatedData['notes'],
                
                // --- DEFAULT NILAI ---
                'photographer_id' => null, 
                'photographer_rate' => 0.00,
                'status' => 'Awaiting DP',
            ]);
            
            // D. BUAT PAYMENT (DP Awal)
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $dpAmountFinal, // <-- DP 50% yang sudah dihitung server
                'payment_type' => 'DP',
                'status' => 'Pending',
            ]);
            
            // E. SIMPAN DETAIL ADDON KE BOOKING_ADDONS (LOGIKA BARU)
            if ($selectedAddons->isNotEmpty()) {
                foreach ($selectedAddons as $addon) {
                    BookingAddon::create([
                        'booking_id' => $booking->id,
                        'addon_id' => $addon->id,
                        'quantity' => 1, // Asumsi quantity 1 dari checkbox
                        'price' => $addon->price, // Harga unit addon
                        'total_price' => $addon->price, // Total harga baris ini (harga * 1)
                    ]);
                }
            }
            
            DB::commit();

            return redirect()->route('admin.new-books.index')
                             ->with('success', 'Pemesanan baru berhasil dibuat dan akun klien telah didaftarkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal membuat booking manual: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pemesanan. Cek log error untuk detailnya.');
        }
    }

    public function edit(Booking $booking)
    {
        // Memuat semua data terkait
        $booking->load('user.clientDetails', 'package', 'bookingAddons.addon'); // <-- Tambahkan 'bookingAddons.addon' untuk data edit
        
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get(); // <-- DATA INI YANG HILANG!

        // Pastikan variabel $addons dikirim ke view
        return view('admin.bookings.edit', compact('booking', 'packages', 'addons')); 
    }

    /**
     * Memperbarui pemesanan yang sudah ada.
     */
    public function update(Request $request, Booking $booking)
    {
        // Mendapatkan user ID yang dikecualikan dari Rule::unique
        $clientId = $booking->client_id;
        $user = $booking->user;

        $validatedData = $request->validate([
            // --- Data User & Client Details ---
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',               
                Rule::unique('users', 'email')->ignore($clientId), 
            ],
            'whatsapp_number' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'faculty_or_major' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            
            // --- Data Booking & Payment ---
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date|after_or_equal:today',
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            'notes' => 'nullable|string',
            
            // --- Kalkulasi Harga ---
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id', // Memastikan ID addon valid

            'grand_total' => 'required|numeric|min:0', // Grand Total dari JS
            'dp_amount' => 'required|numeric|min:0|max:' . $request->input('grand_total'), // DP dari JS
        ]);

        // =========================================================
        // 2. TENTUKAN HARGA BARU dan Update data
        // =========================================================
        
        // Ambil data Package dan Add-ons untuk kalkulasi di backend
        $package = Package::findOrFail($validatedData['package_id']);
        $addons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
        
        $packagePrice = $package->price;
        $addonsTotal = $addons->sum('price');
        $grandTotal = $packagePrice + $addonsTotal;

        // Cek jika harga form sama dengan harga kalkulasi backend
        if ($grandTotal != $validatedData['grand_total']) {
             return back()->withInput()->with('error', 'Kesalahan kalkulasi harga. Silakan coba lagi.');
        }


        DB::beginTransaction();

        try {
            // A. UPDATE USER            
            $user->update([
                'name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
            ]);
            $user = User::findOrFail($clientId);           

            // B. Update Data Client Details (Nama, WA, dll)
            $user->clientDetails->update([
                'full_name' => $validatedData['full_name'],
                'whatsapp_number' => $validatedData['whatsapp_number'],
                'university' => $validatedData['university'],
                'faculty_or_major' => $validatedData['faculty_or_major'],
                // Pastikan Anda juga memasukkan field Instagram jika ada
            ]);            

            // C. Update Data Booking
            $booking->update([
                'package_id' => $validatedData['package_id'],
                'event_date' => $validatedData['event_date'],
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'package_price' => $packagePrice, // <-- Harga Package baru
                'addons_total' => $addonsTotal,   // <-- Total Add-ons baru
                'grand_total' => $grandTotal,     // <-- Grand Total baru
                'notes' => $validatedData['notes'] ?? null, // <-- Catatan baru
            ]);

            // D. Sinkronisasi Add-ons (Logika Kunci)
            // // 1. Hapus semua record BookingAddon yang terkait dengan Booking ini
            //    Ini menghapus Add-ons yang dihapus oleh Admin
            $booking->bookingAddons()->delete(); 
        
            // 2. Buat record BookingAddon baru untuk Add-ons yang baru/tersisa
            if ($addons->isNotEmpty()) {
                foreach ($addons as $addon) {
                    BookingAddon::create([
                        'booking_id' => $booking->id,
                        'addon_id' => $addon->id,
                        'quantity' => 1, // Asumsi Quantity 1
                        'price' => $addon->price,
                        'total_price' => $addon->price, // Harga total baris ini
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.new-books.index')
                ->with('success', 'Pemesanan dan Add-ons berhasil diperbarui.');                                        

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal memperbarui booking manual: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui pemesanan.');
        }
    }

    /**
     * Menghapus pemesanan.
     */
    public function destroy(Booking $booking)
    {
        // PERINGATAN: Penghapusan ini bersifat kaskade (menghapus payment terkait)
        DB::beginTransaction();
        try {
            $booking->payments()->delete(); 
            $booking->delete();

            // Klien tetap ada di database (hanya Booking yang dihapus)

            DB::commit();
            return redirect()->route('admin.new-books.index')
                             ->with('success', 'Pemesanan ' . $booking->order_code . ' berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus pemesanan.');
        }
    }
}