<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Menampilkan daftar semua proyek yang aktif.
     */
    public function index()
    {
        // Ini adalah semua status "job aktif"
        $statuses = [
            'Photographer Assigned',
            'Shooting Completed',
            'Originals Delivered',
            'Edits In Progress',
            'Edits Delivered',
            'Awaiting Final Payment',
            'Pending Final Payment', // <-- Status verifikasi baru kita
            'Fully Paid'
        ];

        $bookings = Booking::whereIn('status', $statuses)
                            ->with(['user.clientDetails', 'package', 'payments']) // Eager load payments
                            ->latest('event_date')
                            ->paginate(15);
                            
        return view('admin.projects.index', compact('bookings'));
    }

    /**
     * Menampilkan halaman verifikasi Final Payment.
     * Mirip seperti NewBookingController@show
     */
    public function show(Booking $booking)
    {
        // Hanya tampilkan jika statusnya 'Pending Final Payment'
        if ($booking->status !== 'Pending Final Payment') {
            abort(404, 'Halaman tidak ditemukan atau status booking tidak sesuai.');
        }

        // Load relasi standar
        $booking->load(['package', 'bookingAddons.addon', 'user.clientDetails']);

        // Ambil data Final Payment yang pending
        $finalPayment = $booking->payments()
                                ->where('payment_type', 'Final Payment')
                                ->where('status', 'Pending')
                                ->latest()
                                ->first();
        
        if (!$finalPayment) {
            abort(404, 'Data final payment yang pending tidak ditemukan.');
        }

        return view('admin.projects.show', compact('booking', 'finalPayment'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        // Daftar status yang BOLEH di-update oleh admin di halaman ini
        $allowedStatuses = [
            'Shooting Completed',
            'Originals Delivered',
            'Edits In Progress',
            'Edits Delivered',
            'Awaiting Final Payment', // Ini adalah trigger untuk menagih klien
            'Project Closed'
        ];

        $request->validate([
            'status' => 'required|string|in:' . implode(',', $allowedStatuses)
        ]);

        try {
            $booking->status = $request->status;
            $booking->save();
            
            return back()->with('success', 'Status booking ' . $booking->order_code . ' berhasil diubah ke: ' . $request->status);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update status: ' . $e->getMessage());
        }
    }

    /**
     * Memproses verifikasi atau penolakan Final Payment.
     */
    // public function verifyFinalPayment(Request $request, Booking $booking)
    // {
    //     $request->validate([
    //         'action' => 'required|string|in:verify,reject',
    //     ]);

    //     // Cari payment yang pending
    //     $finalPayment = $booking->payments()
    //                             ->where('payment_type', 'Final Payment')
    //                             ->where('status', 'Pending')
    //                             ->latest()
    //                             ->first();
        
    //     if (!$finalPayment) {
    //         return redirect()->route('admin.projects.index')->with('error', 'Pembayaran yang pending tidak ditemukan.');
    //     }

    //     try {
    //         DB::beginTransaction();

    //         if ($request->action === 'verify') {
    //             // 1. Update status Booking
    //             $booking->status = 'Fully Paid';
                
    //             // 2. Update status Payment
    //             $finalPayment->status = 'Confirmed';

    //             $message = 'Final payment berhasil diverifikasi.';

    //         } elseif ($request->action === 'reject') {
    //             // 1. Update status Booking
    //             $booking->status = 'Awaiting Final Payment'; // Kembalikan ke 'Awaiting'
                
    //             // 2. Update status Payment
    //             $finalPayment->status = 'Rejected'; // Tandai sebagai ditolak
    //             // (Anda mungkin mau hapus 'proof_image_path' agar klien bisa upload ulang)
    //             // $finalPayment->proof_image_path = null; 

    //             $message = 'Final payment ditolak. Klien perlu upload ulang.';
    //         }

    //         $booking->save();
    //         $finalPayment->save();

    //         DB::commit();

    //         return redirect()->route('admin.projects.index')->with('success', $message);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         \Log::error('Final payment verification failed: ' . $e->getMessage());
    //         return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }
}