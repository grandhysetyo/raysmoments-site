<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking; // Dibutuhkan untuk memuat data Booking
use App\Models\Payment; // Dibutuhkan untuk mengupdate record Payment
use Illuminate\Support\Facades\DB; // Untuk transaksi database

class AdminPaymentsController extends Controller
{
    /**
     * Memverifikasi pembayaran Down Payment (DP) dan mengupdate status Booking.
     * * @param \Illuminate\Http\Request $request
     * @param \App\Models\Booking $booking
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyDP(Request $request, Booking $booking)
    {
        // 1. Validasi
        $request->validate([
            // Input 'status' dari form button (DP Verified atau Cancelled)
            'status' => 'required|in:DP Verified,Cancelled', 
        ]);
        
        $newStatus = $request->status;

        // 2. Transaksi Database
        DB::beginTransaction();

        try {
            
            if ($newStatus === 'DP Verified') {
                
                // Cari record Payment DP yang relevan
                $dpPayment = $booking->payments()
                    ->where('payment_type', 'DP')
                    ->where('status', 'Pending') // Hanya cari yang statusnya masih Pending
                    ->firstOrFail(); // Jika tidak ditemukan, akan throw Exception
                
                // A. Update Status di Tabel Payments (Tanggung Jawab Primer)
                $dpPayment->update(['status' => 'Verified']);
                
                // B. Update Status di Tabel Bookings (Efek dari Payment)
                $booking->update(['status' => 'DP Verified']);
                $message = 'Pembayaran DP berhasil diverifikasi. Status Booking diubah menjadi DP Verified.';

            } elseif ($newStatus === 'Cancelled') {
                
                // Jika dibatalkan, langsung update status Booking
                $booking->update(['status' => 'Cancelled']);
                
                $message = 'Pemesanan telah dibatalkan.';
            }

            DB::commit();

            // 2. Redirect ke halaman daftar pemesanan
            return redirect()->route('admin.new-books.index')
                            ->with('success', 'Status pemesanan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Gagal memproses verifikasi DP: " . $e->getMessage());
            
            return back()->with('error', 'Gagal memproses verifikasi. Error: ' . $e->getMessage());
        }
    }
    public function verifyFullPayment(Request $request, Booking $booking)
    {
        // 1. Temukan kedua record payment
        $dpPayment = $booking->payments()->where('payment_type', 'DP')->latest()->first();
        $finalPayment = $booking->payments()->where('payment_type', 'Final Payment')->latest()->first();

        if (!$dpPayment) {
            return back()->with('error', 'Gagal menemukan data DP payment.');
        }

        try {
            DB::beginTransaction();
            
            // 2. Update status Booking langsung ke 'Fully Paid'
            // Ini akan membuat booking melompati alur 'Awaiting Final Payment'
            $booking->status = 'Fully Paid';
            $booking->save();

            // 3. Konfirmasi DP Payment (yang ada bukti transfernya)
            $dpPayment->status = 'Confirmed';
            $dpPayment->save();

            // 4. Konfirmasi Final Payment (meski tidak ada bukti)
            // Karena admin sudah memverifikasi pembayaran lunas
            if ($finalPayment) {
                $finalPayment->status = 'Confirmed';
                // Opsional: salin jumlah/bukti agar konsisten
                // $finalPayment->amount = $booking->grand_total - $dpPayment->amount;
                $finalPayment->save();
            }

            DB::commit();
            
            return redirect()->route('admin.new-books.index')
                             ->with('success', 'Booking ' . $booking->order_code . ' telah dikonfirmasi LUNAS.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Full payment verification failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal verifikasi pembayaran lunas: ' . $e->getMessage());
        }
    }
}