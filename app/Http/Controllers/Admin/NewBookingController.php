<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class NewBookingController extends Controller
{
    /**
     * Menampilkan tabel semua booking dengan status 'Awaiting DP'.
     */
    public function index()
    {
        $targetStatuses = ['Awaiting DP', 'Pending'];
        // Filter booking yang statusnya Awaiting DP
        $bookings = Booking::whereIn('status', $targetStatuses)
                            ->where('status', 'Pending')
                             // Eager Load: package dan user.clientDetails
                            ->with(['package', 'user.clientDetails'])
                            ->latest()
                            ->paginate(10);
                            
        return view('admin.bookings.new-books-index', compact('bookings'));
    }

    /**
     * Menampilkan detail booking dan bukti transfer.
     */
    public function show(Booking $booking)
    {
        // Cari pembayaran DP yang statusnya Pending
        $dpPayment = $booking->payments()
                             ->where('payment_type', 'DP')
                             ->where('status', 'Pending')
                             ->firstOrFail();

        // Load relasi package
        $booking->load([
            'user.clientDetails', // Klien & detailnya
            'package',            // Detail Paket
            'bookingAddons.addon' // Add-ons yang dipesan
        ]);

        return view('admin.bookings.new-books-show', compact('booking', 'dpPayment'));
    }
}