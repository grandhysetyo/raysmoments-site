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
    public function show(Request $request, $order_code = null)
    {
        $code = $order_code ?? $request->input('order_code');

        if (!$code) {
            return redirect()->route('tracking.index')->with('error', 'Masukkan Kode Pesanan.');
        }

        // 1. Load relasi 'changeRequests' agar kita bisa cek statusnya
        $booking = Booking::where('order_code', $code)
                          ->with([
                              'package', 
                              'payments', 
                              'bookingAddons.addon',
                              // AMBIL DATA REQUEST TERBARU
                              'changeRequests' => function($query) {
                                  $query->latest(); 
                              }
                          ])
                          ->first();

        if (!$booking) {
            return redirect()->route('tracking.index')->with('error', 'Pesanan tidak ditemukan.');
        }

        // 2. Ambil request terbaru untuk dikirim ke View
        $latestChangeRequest = $booking->changeRequests->first();

        // 3. Hitung sisa tagihan (Grand Total - Total Verified)
        $totalPaid = $booking->payments->where('status', 'Verified')->sum('amount');
        $remainingBill = $booking->grand_total - $totalPaid;

        $timeline = $this->getBookingTimeline($booking->status);

        // 4. Kirim variabel ke View
        return view('client.tracking.tracking_show', compact(
            'booking', 
            'timeline', 
            'latestChangeRequest',
            'remainingBill'
        ));
    }
    
    /**
     * Helper function untuk menentukan status timeline.
     * Sesuaikan status ENUM di sini.
     */
    protected function getBookingTimeline($currentStatus)
    {
        $statuses = [
            'Awaiting DP' => 'Menunggu Submission DP Klien',
            'Pending' => 'Menunggu Verifikasi DP Admin',
            'DP Verified' => 'DP Terverifikasi, Jadwal Diamankan',
            'Photographer Assigned' => 'Fotografer Ditugaskan',
            'Awaiting Final Payment' => 'Menunggu Pembayaran Final',
            'Pending Final Payment' => 'Menunggu Verifikasi Final Payment Admin',
            'Fully Paid' => 'Lunas',
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