<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function create(Booking $booking)
    {
        return view('client.payments.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_type'=>'required|in:DP,Final',
            'file'=>'required|file|mimes:jpg,png,pdf|max:2048'
        ]);

        $path = $request->file('file')->store('payments','public');

        Payment::create([
            'booking_id' => $booking->id,
            'payment_type' => $request->payment_type,
            'amount' => $booking->grand_total,
            'file_url' => $path,
            'status' => 'Pending',
        ]);

        return redirect()->route('client.bookings.index')->with('success','Bukti pembayaran berhasil diupload!');
    }
}
