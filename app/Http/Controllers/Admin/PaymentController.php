<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    /**
     * Memverifikasi pembayaran DP, dan mengupdate status booking.
     */
    public function verifyDP(Payment $payment)
    {
        // 1. Pastikan ini adalah pembayaran DP yang belum diverifikasi
        if ($payment->payment_type !== 'DP' || $payment->status !== 'Pending') {
            return back()->with('error', 'Pembayaran tidak valid atau sudah diverifikasi.');
        }

        // 2. Update status pembayaran
        $payment->update([
            'status' => 'Verified',
            'verified_by' => Auth::id(),
            'verified_at' => Carbon::now(),
        ]);
        
        // 3. Update status booking terkait
        $booking = $payment->booking;
        $booking->update(['status' => 'DP Verified']); // Status Siklus Proyek
        
        // 4. Redirect kembali ke halaman detail
        return redirect()->route('admin.new-books.show', $booking) 
                         ->with('success', 'Verifikasi DP berhasil! Status booking diupdate ke DP Verified.');
    }
}