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
                            ->whereIn('status', ['Paid', 'Confirmed', 'Pending'])
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

        $validatedData = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date|after_or_equal:today',
            // --- BARU: Validasi Waktu Sesi (konsisten dengan form booking) ---
            'session_1_time' => 'required|date_format:H:i',
            'session_2_time' => 'nullable|date_format:H:i',
            // ---
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id',
        ]);

        // --- LOGIKA VALIDASI SERVER: BLOKIR DOWNGRADE ---
        
        // 1. Cek status DP lagi di sisi server
        $booking->load('package', 'payments'); // Pastikan data terbaru
        $hasPaidDP = $booking->payments()
                            ->whereIn('status', ['Paid', 'Confirmed', 'Pending'])
                            ->exists();

        if ($hasPaidDP) {
            $currentPrice = $booking->package->price;
            
            // Ambil paket BARU yang diminta
            $newPackage = Package::findOrFail($validatedData['package_id']);
            $newPrice = $newPackage->price;

            // 2. Jika paket baru lebih murah, tolak
            if ($newPrice < $currentPrice) {
                return back()->with('error', 'Gagal mengajukan perubahan. Anda tidak dapat melakukan downgrade paket setelah pembayaran DP diproses.')
                             ->withInput();
            }
        }
        // --- AKHIR LOGIKA VALIDASI SERVER ---

        DB::beginTransaction();
        try {
            // Kalkulasi ulang harga berdasarkan data BARU
            $newPackage = Package::findOrFail($validatedData['package_id']);
            $selectedAddons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
            
            $newPackagePrice = $newPackage->price;
            $newAddonsTotal = $selectedAddons->sum('price');
            $newGrandTotal = $newPackagePrice + $newAddonsTotal;

            // Kumpulkan semua data yang diminta untuk disimpan sebagai JSON
            $requestedData = [
                'package_id' => $newPackage->id,
                'package_name' => $newPackage->name,
                'event_date' => $validatedData['event_date'],
                // --- BARU: Simpan data sesi ---
                'session_1_time' => $validatedData['session_1_time'],
                'session_2_time' => $validatedData['session_2_time'] ?? null,
                // ---
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'notes' => $validatedData['notes'],
                'addons' => $selectedAddons->map(function($addon) {
                    return ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price];
                })->toArray(),
                'new_package_price' => $newPackagePrice,
                'new_addons_total' => $newAddonsTotal,
                'new_grand_total' => $newGrandTotal,
            ];

            // Buat entri permintaan perubahan
            BookingChangeRequest::create([
                'booking_id' => $booking->id,
                'client_id' => auth()->id(),
                'requested_data' => json_encode($requestedData),
                'status' => 'Pending',
            ]);

            DB::commit();

            // Arahkan klien kembali ke halaman tracking
            return redirect()->route('tracking.show', $booking->order_code)
                             ->with('success', 'Permintaan perubahan jadwal dan detail telah diajukan dan sedang ditinjau oleh Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan permintaan perubahan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengajukan permintaan. Silakan coba lagi.');
        }
    }
}