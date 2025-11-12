<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Penting untuk mendapatkan ID user
use App\Models\Booking;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard fotografer.
     */
    public function index()
    {
        $photographerId = Auth::id(); // <-- Mendapatkan ID fotografer yang sedang login

        // === 1. DATA UNTUK KARTU STATISTIK ===

        // Kartu 1: Jadwal Akan Datang
        $upcomingJobsCount = Booking::where('photographer_id', $photographerId)
            ->where('event_date', '>=', Carbon::today())
            ->whereIn('status', ['DP', 'AddOn', 'FinalPayment']) // Status "siap jalan"
            ->count();

        // Kartu 2: Proyek Selesai (Perlu Upload)
        $pendingUploadsCount = Booking::where('photographer_id', $photographerId)
            ->whereIn('status', ['DeliveryOriginal', 'Editing']) // Status "selesai, tunggu foto"
            ->count();
            
        // Kartu 3: Total Proyek Selesai (Completed)
        $completedProjectsCount = Booking::where('photographer_id', $photographerId)
            ->where('status', 'Completed')
            ->count();

        
        // === 2. DATA UNTUK TABEL/LIST AKTIVITAS ===

        // Mengambil 5 jadwal terdekat
        $upcomingSchedule = Booking::with('package')
            ->where('photographer_id', $photographerId)
            ->where('event_date', '>=', Carbon::today())
            ->whereIn('status', ['DP', 'AddOn', 'FinalPayment'])
            ->orderBy('event_date', 'asc') // Urutkan dari yang paling dekat
            ->take(5)
            ->get();
            

        // === 3. KIRIM SEMUA DATA KE VIEW ===
        
        return view('photographer.dashboard', compact(
            'upcomingJobsCount',
            'pendingUploadsCount',
            'completedProjectsCount',
            'upcomingSchedule'
        ));
    }
}