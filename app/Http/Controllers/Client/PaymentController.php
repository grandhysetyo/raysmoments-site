<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Menyimpan data pembayaran baru dari modal.
     */
    /**
     * Menyimpan (MENG-UPDATE) data pembayaran dari modal.
     */
    public function store(Request $request, $order_code)
    {
        // 1. Temukan booking berdasarkan order_code
        $booking = Booking::where('order_code', $order_code)->firstOrFail();

        // 2. Validasi input
        $validated = $request->validate([
            'payment_type' => 'required|string|in:DP,Final',
            'amount' => 'required|numeric|min:1',
            'proof_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // 3. Temukan data payment yang HARUS di-update
            //    Kita cari payment yang:
            //    - Sesuai dengan booking_id
            //    - Sesuai dengan payment_type (DP atau Final Payment)
            //    - Statusnya 'Pending' (masih kosong) ATAU 'Rejected' (perlu upload ulang)
            
            $paymentToUpdate = Payment::where('booking_id', $booking->id)
                ->where('payment_type', $validated['payment_type'])
                ->where(function ($query) {
                    // Cari yang statusnya Pending TAPI belum ada bukti
                    $query->where('status', 'Pending') 
                          ->whereNull('proof_url');
                    // ATAU cari yang di-reject admin
                    $query->orWhere('status', 'Rejected'); 
                })
                ->orderBy('created_at', 'desc') // Ambil yang paling baru jika ada duplikat
                ->first();

            // Jika tidak ditemukan payment yang sesuai (misal: sudah 'Verified')
            if (!$paymentToUpdate) {
                // Mungkin payment DP sudah Verified, dan ini adalah upload Final Payment
                // Coba cari lagi HANYA berdasarkan tipe
                $paymentToUpdate = Payment::where('booking_id', $booking->id)
                                ->where('payment_type', $validated['payment_type'])
                                ->whereNull('proof_url')
                                ->first();

                // Jika tetap tidak ketemu, berarti ada masalah
                if (!$paymentToUpdate) {
                     return back()->with('error', 'Tidak dapat menemukan data tagihan yang sesuai untuk di-update. Silakan hubungi admin.');
                }
            }
            
            // 4. Proses upload file
            $path = $request->file('proof_url')->store('payment_proofs', 'public');

            // 5. UPDATE data payment yang ada
            $paymentToUpdate->update([
                'amount' => $validated['amount'],
                'proof_url' => $path,
                'status' => 'Pending' // Set (atau set ulang) status ke 'Pending' untuk direview Admin
            ]);

            if ($validated['payment_type'] === 'DP' && $booking->status === 'Awaiting DP') {
                // Jika ini DP, set status booking ke 'Pending' (untuk Verifikasi DP)
                $booking->status = 'Pending';
            } elseif ($validated['payment_type'] === 'Final' && $booking->status === 'Awaiting Final Payment') {
                // Jika ini Final Payment, set status ke 'Pending Final Payment'
                $booking->status = 'Pending Final Payment';
            }

            $booking->save();
            // 7. Kembalikan ke halaman tracking
            return redirect()->route('tracking.show', $booking->order_code)
                             ->with('success', 'Bukti pembayaran berhasil diupload dan sedang menunggu konfirmasi admin.');

        } catch (\Exception $e) {
            // 8. Jika terjadi error
            \Log::error('Payment update failed: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengupload bukti pembayaran. Silakan coba lagi.');
        }
    }
}
