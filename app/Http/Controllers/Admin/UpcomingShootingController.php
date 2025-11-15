<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User; // Model User (untuk fotografer)
use App\Models\PhotographerRate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

class UpcomingShootingController extends Controller
{
    /**
     * Menampilkan daftar booking yang siap di-assign (DP Verified).
     */
    public function index()
    {
        // PERUBAHAN: Status DP Verified ATAU Photographer Assigned
        // Agar yang sudah di-assign tetap muncul di list ini
        $statuses = ['DP Verified', 'Photographer Assigned','Fully Paid'];
        
        $bookings = Booking::whereIn('status', $statuses)
                            ->with(['package', 'user.clientDetails', 'photographer'])
                            ->latest('event_date')
                            ->paginate(15);
                            
        return view('admin.upcoming.index', compact('bookings'));
    }

    /**
     * Menampilkan form untuk assign fotografer.
     */
    public function show(Booking $booking)
    {
        // Ambil semua user yang rolenya 'photographer'
        $photographers = User::where('role', 'photographer')->orderBy('name')->get();
        
        return view('admin.upcoming.show', compact('booking', 'photographers'));
    }

    /**
     * =========================================================
     * PERUBAHAN LOGIKA UTAMA ADA DI SINI
     * Menyimpan data assignment fotografer.
     * =========================================================
     */
    public function assign(Request $request, Booking $booking)
    {
        // Validasi baru:
        $request->validate([
            'photographer_id' => 'required|exists:users,id',
            'photographer_rate' => 'required|numeric|min:0',
            'photographer_other_costs' => 'nullable|numeric|min:0', // <-- Validasi baru
        ]);

        // 1. Simpan data assignment
        $booking->photographer_id = $request->photographer_id;
        $booking->photographer_rate = $request->photographer_rate;
        
        // <-- SIMPAN DATA BARU
        // Gunakan ?? 0 untuk memastikan nilainya tidak null jika kosong
        $booking->photographer_other_costs = $request->photographer_other_costs ?? 0;
        
       // ======================================================
        // 3. LOGIKA YANG BENAR: Cek relasi payments
        // ======================================================
        
        // Kita hitung ada berapa record payment yang sudah 'Verified'
        // (Berdasarkan Payment.php, kolomnya adalah 'status')
        $verifiedPaymentsCount = $booking->payments()
                                          ->where('status', 'Verified')
                                          ->count();

        // Di sistem Anda (dari BookingController), setiap booking
        // selalu dibuatkan 2 record payment (DP dan Final).
        //
        // Jika count == 2, berarti DP dan Final sudah 'Verified'
        // (Ini adalah skenario Bayar Lunas di Awal)
        
        if ($verifiedPaymentsCount == 2) {
            
            // Skenario A: Bayar Lunas di Awal.
            // Status booking langsung 'Fully Paid'.
            $booking->status = 'Fully Paid'; 
            
        } else {            
            // Skenario B: Bayar DP 50%. (Count == 1)
            // Status booking menjadi 'Photographer Assigned'.
            $booking->status = 'Photographer Assigned'; 
        }
        
        $booking->save();

        return redirect()->route('admin.upcoming.index')
                         ->with('success', 'Fotografer berhasil ditugaskan untuk ' . $booking->order_code);
    }

    /**
     * Memproses refund dan membatalkan booking.
     */
    public function refund(Request $request, Booking $booking)
    {
        // 1. Cek apakah sudah ada refund pending (agar tidak double)
        $existingRefund = Payment::where('booking_id', $booking->id)
                                 ->where('payment_type', 'Refund')
                                 ->where('status', 'Pending')
                                 ->exists();

        if ($existingRefund) {
            return back()->with('error', 'Sudah ada permintaan refund yang pending untuk pesanan ini.');
        }

        // Kita gunakan transaction
        try {
            DB::beginTransaction();

            // 2. Update status booking menjadi 'Cancelled'
            $booking->status = 'Cancelled'; // Pastikan 'Cancelled' ada di ENUM booking
            $booking->save();

            // 3. BUAT RECORD 'Refund' BARU (Sesuai permintaan Anda)
            Payment::create([
                'booking_id' => $booking->id,
                'client_id' => $booking->client_id,
                'amount' => 0, // Dibuat 0. Admin/Owner akan update manual saat memproses
                'payment_type' => 'Refund', // Tipe baru dari ENUM
                'status' => 'Pending',      // Status 'Pending'
                'proof_url' => null,
            ]);
            
            DB::commit(); // Selesaikan transaksi

            return redirect()->route('admin.upcoming.index')
                             ->with('success', 'Pesanan ' . $booking->order_code . ' telah dibatalkan. Permintaan refund (pending) telah dibuat.');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua jika ada error
            
            // Log error
            \Log::error('Refund creation failed: ' . $e->getMessage());
            
            // Beri tahu admin apa yang error
            return back()->with('error', 'Gagal membatalkan pesanan. Error: ' . $e->getMessage());
        }
    }


    /**
     * [AJAX] Mengambil daftar rate untuk fotografer tertentu.
     * (Fungsi ini tidak berubah, masih sangat berguna)
     */
    public function getRates(User $photographer)
    {
        // 1. Pastikan user ini adalah fotografer
        if ($photographer->role !== 'photographer') {
            return response()->json([], 404);
        }

        // 2. Ambil profile-nya
        $profile = $photographer->photographerProfile;

        // 3. Cek jika dia punya profile
        if (!$profile) {
            // Fotografer ini belum punya profile, jadi tidak punya rates
            return response()->json([], 404);
        }

        // 4. Ambil rates DARI PROFILE
        // Kita gunakan 'city' sebagai nama dan 'base_rate' sebagai harga
        $rates = $profile->rates()->get(['id', 'city', 'base_rate', 'transport_fee']);
        
        // 5. Kita format di sini agar JS-nya tetap simpel
        $formattedRates = $rates->map(function ($rate) {
            // Asumsi harga = base_rate + transport_fee
            // Sesuaikan jika logikanya berbeda
            $totalPrice = $rate->base_rate + $rate->transport_fee;

            return [
                // 'id' tidak kita pakai, tapi bagus untuk ada
                'id' => $rate->id, 
                // Kita "buat" rate_name dari 'city'
                'rate_name' => 'Tarif Kota: ' . $rate->city, 
                // 'price' adalah value yang akan disalin ke input
                'price' => $totalPrice 
            ];
        });

        return response()->json($formattedRates);
    }
}