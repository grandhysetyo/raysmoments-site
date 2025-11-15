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
            'session_1_time' => 'required|date_format:H:i',
            'session_2_time' => 'nullable|date_format:H:i',
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            'notes' => 'nullable|string',
            
            // --- Kalkulasi Harga ---
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id', // Memastikan ID addon valid
            'payment_option' => 'required|string|in:dp,full',
            'grand_total' => 'required|numeric|min:0', // Grand Total dari JS
            'dp_amount' => 'required|numeric|min:0|max:' . $request->input('grand_total'), // DP dari JS
        ]);

        // 1. RE-KALKULASI HARGA DI SISI SERVER (Security Check)
        $package = Package::findOrFail($validatedData['package_id']);
        $selectedAddons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
        
        $packagePrice = $package->price;
        $addonsTotal = $selectedAddons->sum('price');
        $grandTotalFinal = $packagePrice + $addonsTotal;
        
        // 2. Final Security Check (Cek sebagai string terformat)
        $jsGrandTotal = number_format(floatval($validatedData['grand_total']), 2, '.', '');
        $phpGrandTotal = number_format($grandTotalFinal, 2, '.', '');

        if ($jsGrandTotal != $phpGrandTotal) {
            \Log::error("Price mismatch on create: JS ($jsGrandTotal) != PHP ($phpGrandTotal)");
            return back()->with('error', 'Terjadi ketidaksesuaian harga (Kalkulasi PHP: Rp '. $phpGrandTotal .'). Harap hitung ulang.')->withInput();
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
                'session_1_time' => $validatedData['session_1_time'],
                'session_2_time' => $validatedData['session_2_time'] ?? null,
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'notes' => $validatedData['notes'],
                
                // --- DEFAULT NILAI ---
                'photographer_id' => null, 
                'photographer_rate' => 0.00,
                'status' => 'Awaiting DP',
            ]);
            
            // D. BUAT PAYMENT (DP Awal)
            $paymentOption = $validatedData['payment_option'];
            $dpAmount = 0;
            $finalAmount = 0;

            // HARUS GUNAKAN $grandTotalFinal YANG SUDAH DIHITUNG DI BACKEND
            if ($paymentOption === 'full') {
                // Klien bayar lunas 100% di awal
                $dpAmount = $grandTotalFinal;
                $finalAmount = 0;
                
            } else {
                // Alur normal, DP 50%
                $dpAmount = $grandTotalFinal * 0.5;
                $finalAmount = $grandTotalFinal * 0.5;
            }

            // Saat admin membuat manual, status booking harus 'Awaiting DP'
            // (Pastikan status booking Anda sesuai)
            $booking->update(['status' => 'Awaiting DP']);

            // Buat Payment DP
            $booking->payments()->create([
                'amount' => $dpAmount,
                'payment_type' => 'DP',
                'status' => 'Pending', // Admin harus upload bukti & verifikasi manual nanti
                'proof_url' => null, 
                'verified_by' => null,
            ]);

            // Buat Payment Final
            $booking->payments()->create([
                'payment_type' => 'Final',
                'amount' => $finalAmount,
                // Jika 0, status 'Verified', jika > 0, status 'Pending'
                'status' => $finalAmount > 0 ? 'Pending' : 'Verified',
                'proof_url' => null,
                'verified_by' => null,
            ]);
            
            // E. SIMPAN DETAIL ADDON KE BOOKING_ADDONS (LOGIKA BARU)
            if ($selectedAddons->isNotEmpty()) {
                foreach ($selectedAddons as $addon) {
                    BookingAddon::create([
                        'booking_id' => $booking->id,
                        'addon_id' => $addon->id,
                        'quantity' => 1, // Asumsi quantity 1 dari checkbox
                        'price' => $addon->price, // Harga unit addon
                        'grand_total' => $addon->price, // Total harga baris ini (harga * 1)
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
            'session_1_time' => 'required|date_format:H:i',
            'session_2_time' => 'nullable|date_format:H:i',
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            'notes' => 'nullable|string',
            
            // --- Kalkulasi Harga ---
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id', // Memastikan ID addon valid

            'grand_total' => 'required|numeric|min:0', // Grand Total dari JS
            'dp_amount' => 'required|numeric|min:0|max:' . $request->input('grand_total'), // DP dari JS
            'payment_option' => 'required|string|in:dp,full'
        ]);

        // =========================================================
        // 2. TENTUKAN HARGA BARU dan Update data
        // =========================================================
        // 1. Ambil data Addons untuk kalkulasi di backend (WAJIB UNTUK KEAMANAN)
        $selectedAddons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
        $package = Package::findOrFail($validatedData['package_id']);
        
        $packagePrice = $package->price;
        $addonsTotal = $selectedAddons->sum('price');
        $grandTotalFinal = $packagePrice + $addonsTotal;

        // 2. Final Security Check (Cek sebagai string terformat)
        $jsGrandTotal = number_format(floatval($validatedData['grand_total']), 2, '.', '');
        $phpGrandTotal = number_format($grandTotalFinal, 2, '.', '');

        if ($jsGrandTotal != $phpGrandTotal) {
            \Log::error("Price mismatch on update $booking->id: JS ($jsGrandTotal) != PHP ($phpGrandTotal)");
            return back()->with('error', 'Terjadi ketidaksesuaian harga (Kalkulasi PHP: Rp '. $phpGrandTotal .'). Harap hitung ulang.')->withInput();
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
                'session_1_time' => $validatedData['session_1_time'],
                'session_2_time' => $validatedData['session_2_time'] ?? null,
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'package_price' => $packagePrice, // <-- Harga Package baru
                'addons_total' => $addonsTotal,   // <-- Total Add-ons baru
                'grand_total' => $grandTotalFinal,     // <-- Grand Total baru
                'notes' => $validatedData['notes'] ?? null, // <-- Catatan baru
            ]);

            // D. BUAT PAYMENT (DP Awal)
            $paymentOption = $validatedData['payment_option'];
            $dpAmount = 0;
            $finalAmount = 0;

            if ($paymentOption === 'full') {
                $dpAmount = $grandTotalFinal;
                $finalAmount = 0;
            } else {
                $dpAmount = $grandTotalFinal * 0.5;
                $finalAmount = $grandTotalFinal * 0.5;
            }

            // Cari payment DP yang ada dan UPDATE, JANGAN CREATE
            $dpPayment = $booking->payments()->where('payment_type', 'DP')->first();
            if ($dpPayment) {
                // Hanya update amount jika statusnya masih Pending
                if ($dpPayment->status == 'Pending') {
                    $dpPayment->update(['amount' => $dpAmount]);
                }
            } else {
                // Fallback jika anehnya datanya tidak ada
                $booking->payments()->create([
                    'amount' => $dpAmount, 'payment_type' => 'DP', 'status' => 'Pending',
                ]);
            }

            // Cari payment Final yang ada dan UPDATE
            $finalPayment = $booking->payments()->where('payment_type', 'Final')->first();
            if ($finalPayment) {
                // Hanya update amount jika statusnya masih Pending (atau Verified jika 0)
                if ($finalPayment->status == 'Pending' || $finalPayment->status == 'Verified') {
                    $finalPayment->update([
                        'amount' => $finalAmount,
                        'status' => $finalAmount > 0 ? 'Pending' : 'Verified',
                    ]);
                }
            } else {
                // Fallback jika anehnya datanya tidak ada
                $booking->payments()->create([
                    'amount' => $finalAmount,
                    'payment_type' => 'Final',
                    'status' => $finalAmount > 0 ? 'Pending' : 'Verified',
                ]);
            }

            // E. Sinkronisasi Add-ons (Logika Kunci)
            // // 1. Hapus semua record BookingAddon yang terkait dengan Booking ini
            //    Ini menghapus Add-ons yang dihapus oleh Admin
            $addonsData = [];
            if ($selectedAddons->isNotEmpty()) {
                foreach ($selectedAddons as $addon) {
                    $addonsData[$addon->id] = [
                        'quantity' => 1,
                        'price' => $addon->price,
                        'grand_total' => $addon->price,
                    ];
                }
            }
            // Sync akan hapus yg lama, masukkan yg baru.
            $booking->addons()->sync($addonsData);

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