<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingChangeRequest;

class BookingChangeRequestController extends Controller
{
    /**
     * Menyimpan pengajuan perubahan baru dari Klien.
     */
    public function store(Request $request, Booking $booking)
    {
        // Pastikan hanya klien yang login (jika ada) atau pemilik booking yang bisa
        // (Kita asumsikan $booking didapat dari URL/tracking)

        // 1. Validasi data baru
        $validated = $request->validate([
            'new_event_date' => 'required|date|after_or_equal:today',
            'new_event_location' => 'required|string|max:255',
            'new_event_city' => 'required|string|max:255',
            'reason' => 'required|string|min:10',
        ]);

        // 2. Cek apakah sudah ada pengajuan yang Pending
        $existingRequest = $booking->pendingChangeRequest()->exists();
        if ($existingRequest || $booking->status === 'Awaiting Change Approval') {
            return back()->with('error', 'Anda sudah memiliki 1 pengajuan perubahan yang sedang ditinjau Admin.');
        }

        // 3. Buat record Pengajuan Perubahan
        BookingChangeRequest::create([
            'booking_id' => $booking->id,
            'old_event_date' => $booking->event_date,
            'old_event_location' => $booking->event_location,
            'old_event_city' => $booking->event_city,
            'new_event_date' => $validated['new_event_date'],
            'new_event_location' => $validated['new_event_location'],
            'new_event_city' => $validated['new_event_city'],
            'reason' => $validated['reason'],
            'status' => 'Pending',
        ]);

        // 4. Update status Booking utama
        $booking->update([
            'status' => 'Awaiting Change Approval' 
            // PASTIKAN status ENUM 'Awaiting Change Approval' ada di tabel bookings
        ]);

        return redirect()->route('client.tracking.show', $booking->order_code)
                         ->with('success', 'Pengajuan perubahan jadwal/lokasi telah terkirim. Admin akan segera meninjau.');
    }
}