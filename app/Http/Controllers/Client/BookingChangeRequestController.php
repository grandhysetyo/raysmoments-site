<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
use App\Models\BookingChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingChangeRequestController extends Controller
{
    /**
     * Menampilkan form untuk membuat permintaan perubahan.
     */
    public function show($order_code)
    {
        $booking = Booking::where('order_code', $order_code)->firstOrFail();

        // 1. Muat relasi yang diperlukan
        $booking->load('package', 'payments', 'bookingAddons');

        // 2. Cek apakah klien sudah melakukan pembayaran DP
        //    Kita anggap 'Pending' (menunggu konfirmasi) juga sudah dihitung
        $hasPaidDP = $booking->payments()
                            ->whereIn('status', ['Verified'])
                            ->exists();

        // 3. Dapatkan harga paket saat ini
        $currentPrice = $booking->package->price;

        // 4. Siapkan query untuk paket yang tersedia
        $availablePackages = Package::where('is_active', true);

        // --- LOGIKA BARU: BLOKIR DOWNGRADE ---
        // Jika sudah bayar DP, hanya tampilkan paket dengan harga
        // SAMA ATAU LEBIH MAHAL (>=)
        if ($hasPaidDP) {
            $availablePackages->where('price', '>=', $currentPrice);
        }
        // --- AKHIR LOGIKA BARU ---

        // Ambil hasil paket yang sudah difilter
        $packages = $availablePackages->get();
        
        // Ambil semua addons
        $addons = Addon::where('is_active', true)->get();

        // Ambil addons yang saat ini dipilih
        $currentAddonIds = $booking->bookingAddons->pluck('addon_id')->toArray();

        return view('client.bookings.change_requests', compact(
            'booking', 
            'packages', 
            'addons', 
            'currentAddonIds',
            'hasPaidDP' // Kirim status DP untuk info di view (opsional)
        ));
    }

    /**
     * Menyimpan permintaan perubahan baru.
     */
    public function store(Request $request, $order_code)
    {
        $booking = Booking::where('order_code', $order_code)->firstOrFail();

        $validated = $request->validate([
            'package_id'     => 'required|exists:packages,id',
            'addons'         => 'nullable|array',           // Validasi array addons
            'addons.*'       => 'exists:addons,id',
            'event_date' => 'required|date|after_or_equal:today',
            'session_1_time' => 'required|date_format:H:i',
            'session_2_time' => 'nullable|date_format:H:i',
            // ---
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            'reason'         => 'required|string|max:1000',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id',
        ]);

        if (BookingChangeRequest::where('booking_id', $booking->id)->where('status', 'Pending')->exists()) {
            return back()->with('error', 'Permintaan sebelumnya masih diproses.');
        }
    
        DB::beginTransaction();
        try {
            // 1. HITUNG UANG YANG SUDAH MASUK
            $totalPaidVerified = $booking->payments()->where('status', 'Verified')->sum('amount');
    
            // 2. HITUNG HARGA BARU (FIXED LOGIC)
            // Ambil harga Paket Baru
            $newPackage = Package::findOrFail($validated['package_id']);
            
            // Ambil harga Add-ons dari INPUT USER (Bukan database lama)
            $selectedAddonIds = $request->input('addons', []); 
            $newAddonsTotal = 0;
            
            if (!empty($selectedAddonIds)) {
                $newAddonsTotal = Addon::whereIn('id', $selectedAddonIds)->sum('price');
            }
    
            // Grand Total Baru = Paket Baru + Addons Baru
            $newGrandTotal = $newPackage->price + $newAddonsTotal;
    
            // 3. LOGIKA KALKULASI SELISIH (Sama seperti sebelumnya)
            $additionalCost = 0;
    
            if ($totalPaidVerified > 0) {
                // Jika sudah bayar DP/Lunas
                $isFullyPaid = in_array($booking->status, ['Final Payment Verified', 'Project Completed', 'Project Finished']);
    
                if ($isFullyPaid) {
                    $additionalCost = $newGrandTotal - $totalPaidVerified;
                } else {
                    // Kejar target DP 50% dari total baru
                    $targetNewDP = $newGrandTotal * 0.50;
                    if ($targetNewDP > $totalPaidVerified) {
                        $additionalCost = $targetNewDP - $totalPaidVerified;
                    }
                }
            }
            // Jika belum bayar ($totalPaidVerified == 0), cost tetap 0 (nanti update tagihan DP)
    
            $additionalCost = max(0, $additionalCost);
    
            // 4. SIMPAN REQUEST
            BookingChangeRequest::create([
                'booking_id' => $booking->id,
                'new_package_id' => $newPackage->id,
                'new_addons' => json_encode($selectedAddonIds),
                'additional_cost' => $additionalCost,
                
                'old_event_date' => $booking->event_date,
                'old_event_location' => $booking->event_location,
                'old_event_city' => $booking->event_city,
                
                'new_event_date' => $validated['event_date'],
                'new_event_location' => $validated['event_location'],
                'new_event_city' => $validated['event_city'],
                
                'reason' => $validated['reason'],
                'status' => 'Pending',
            ]);
    
            DB::commit();
            return redirect()->route('tracking.show', $booking->order_code)->with('success', 'Permintaan diajukan.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}