<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class TrackingController extends Controller
{
    /**
     * Menampilkan formulir input order code.
     */
    public function index()
    {
        return view('client.tracking.tracking_index');
    }

    /**
     * Menampilkan detail booking berdasarkan order code.
     */
    public function show($order_code)
    {
        // Cari booking berdasarkan order_code
        $booking = Booking::where('order_code', $order_code)
                          ->with(['package', 'user.clientDetails', 'bookingAddons.addon', 'payments'])
                          ->first();

        if (!$booking) {
            // Jika booking tidak ditemukan
            return redirect()->route('client.tracking.index')
                             ->with('error', 'Kode pesanan tidak ditemukan. Harap periksa kembali.');
        }

        // Tentukan data timeline (sesuaikan dengan ENUM status Anda)
        $timeline = $this->getBookingTimeline($booking->status);
        
        return view('client.tracking.tracking_show', compact('booking', 'timeline'));
    }
    
    /**
     * Helper function untuk menentukan status timeline.
     * Sesuaikan status ENUM di sini.
     */
    protected function getBookingTimeline($currentStatus)
    {
        $statuses = [
            'Awaiting DP Submission' => 'Menunggu Submission DP Klien',
            'Awaiting DP' => 'Menunggu Verifikasi DP Admin',
            'DP Verified' => 'DP Terverifikasi, Jadwal Diamankan',
            'Awaiting Final Payment' => 'Menunggu Pembayaran Final',
            'Fully Paid' => 'Lunas, Siap Produksi',
            'Photographer Assigned' => 'Fotografer Ditugaskan',
            'Shooting Completed' => 'Sesi Foto Selesai',
            'Edits In Progress' => 'Proses Editing',
            'Edits Delivered' => 'Hasil Editing Dikirim',
            'Project Closed' => 'Proyek Selesai',
            'Cancelled' => 'Dibatalkan'
        ];

        $current = false;
        $completed = true;
        $timeline = [];

        foreach ($statuses as $key => $label) {
            $is_active = false;
            $is_completed = false;

            if ($key === $currentStatus) {
                $current = true;
                $is_active = true;
                $is_completed = false;
            } elseif ($current) {
                // Semua status setelah status saat ini adalah pending
                $is_active = false;
                $is_completed = false;
            } else {
                // Semua status sebelum status saat ini adalah completed
                $is_completed = true;
            }

            // Status Cancelled (Dibatalkan) adalah pengecualian yang harus berhenti
            if ($key === 'Cancelled' && $currentStatus === 'Cancelled') {
                $is_active = true;
                $is_completed = false;
                $timeline = [['key' => 'Cancelled', 'label' => 'PESANAN DIBATALKAN', 'is_active' => true, 'is_completed' => false]];
                break;
            }

            if ($key !== 'Cancelled') {
                $timeline[] = [
                    'key' => $key,
                    'label' => $label,
                    'is_active' => $is_active,
                    'is_completed' => $is_completed,
                ];
            }
            
            if ($currentStatus === 'Project Closed') {
                 $is_completed = true; // semua langkah selesai
            }
        }
        return $timeline;
    }
}