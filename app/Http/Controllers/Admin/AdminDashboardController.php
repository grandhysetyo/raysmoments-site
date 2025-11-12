<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking; // <-- Impor model
use App\Models\Payment; // <-- Impor model
use App\Models\User;    // <-- Impor model
use Illuminate\Http\Request;
use Illuminate\Support\Carbon; // <-- Impor Carbon untuk filter tanggal

class AdminDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data statistik.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mendapatkan tanggal awal dan akhir bulan ini
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // === 1. DATA UNTUK KARTU STATISTIK ===

        // Kartu 1: Pendapatan (Hanya pembayaran yang 'Verified' bulan ini)
        $revenueThisMonth = Payment::where('status', 'Verified')
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Kartu 2: Pembayaran Pending (Hanya yang statusnya 'Pending')
        $pendingPaymentsCount = Payment::where('status', 'Pending')->count();

        // Kartu 3: Booking Baru (Booking yang dibuat bulan ini)
        $newBookingsCount = Booking::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        
        // Kartu 4: Fotografer Aktif
        $activePhotographersCount = User::where('role', 'photographer')
            ->where('status', 'active')
            ->count();


        // === 2. DATA UNTUK TABEL/LIST AKTIVITAS ===

        // Mengambil 5 booking terbaru untuk ditampilkan
        $recentBookings = Booking::with('package') // 'package' adalah nama relasi di model Booking
            ->latest() // Mengurutkan dari yang terbaru
            ->take(5)  // Ambil 5 saja
            ->get();
            
        // Mengambil 5 pembayaran pending terbaru
        $recentPendingPayments = Payment::with('booking') // Relasi ke booking
            ->where('status', 'Pending')
            ->latest()
            ->take(5)
            ->get();


        // === 3. KIRIM SEMUA DATA KE VIEW ===
        
        // Data ini dikirim ke view 'admin.dashboard'
        return view('admin.dashboard', compact(
            'revenueThisMonth',
            'pendingPaymentsCount',
            'newBookingsCount',
            'activePhotographersCount',
            'recentBookings',
            'recentPendingPayments'
        ));
    }
}