<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Package;
use App\Models\BookingChangeRequest;
use App\Models\Addon;
use Illuminate\Support\Str;
use App\Models\User;


class BookingController extends Controller
{
  
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();
        return view('client.bookings.create', compact('packages','addons'))->with('isEditMode', false);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            // --- Data User & Client Details ---
            'email' => 'required|email|max:255|unique:users,email', 
            'full_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'faculty_or_major' => 'nullable|string|max:255',
            
            // --- Data Booking ---
            'package_id' => 'required|exists:packages,id',
            'event_date' => 'required|date|after_or_equal:today',
            'session_1_time' => 'required|date_format:H:i',
            'session_2_time' => 'nullable|date_format:H:i',
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            
            // --- Kalkulasi Harga (dari form) ---
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id',
            'grand_total' => 'required|numeric|min:0', // Nilai dari JS
            'package_price_hidden' => 'required|numeric|min:0',
            'payment_option' => 'required|string|in:dp,full'
        ]);
        
        // 1. Ambil data Addons untuk kalkulasi di backend
        $selectedAddons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
        $package = Package::findOrFail($validatedData['package_id']);

        $packagePrice = $package->price;
        $addonsTotal = $selectedAddons->sum('price');
        $grandTotalFinal = $packagePrice + $addonsTotal;

        // 2. Final Security Check (Pastikan harga tidak dimanipulasi)
        if (floatval($validatedData['grand_total']) != $grandTotalFinal) {
             return back()->with('error', 'Terjadi ketidaksesuaian harga. Harap hitung ulang.')->withInput();
        }

        DB::beginTransaction();

        try {
            // A. BUAT USER BARU (REGISTRASI ON-THE-FLY)
            $user = User::create([
                'name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
                'password' => Hash::make('12345678'), // Password default
                'role' => 'client',
                'status' => 'active',
            ]);

            // B. BUAT DETAIL KLIEN
            $user->clientDetails()->create([
                'full_name' => $validatedData['full_name'],
                'whatsapp_number' => $validatedData['whatsapp_number'],
                'university' => $validatedData['university'],
                'faculty_or_major' => $validatedData['faculty_or_major'],
                'instagram' => $validatedData['instagram'],
            ]);

            // C. BUAT BOOKING
            $booking = $user->bookings()->create([
                'order_code' => 'CLI-' . time(), 
                'package_id' => $validatedData['package_id'],
                'package_price' => $packagePrice,
                'addons_total' => $addonsTotal, 
                'grand_total' => $grandTotalFinal, 
                'event_date' => $validatedData['event_date'],
                'session_1_time' => $validatedData['session_1_time'],
                'session_2_time' => $validatedData['session_2_time'] ?? null,
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'status' => 'Awaiting DP',
            ]);

            // D. BUAT PAYMENT (Record DP yang belum dibayarkan)
            $paymentOption = $request->input('payment_option');
            $dpAmount = 0;
            $finalAmount = 0;

            // HARUS GUNAKAN $grandTotalFinal YANG SUDAH DIHITUNG DI ATAS
            if ($paymentOption === 'full') {
                // Klien bayar lunas 100% di awal
                // Kita "tipu" sistem dgn memasukkan 100% ke DP
                $dpAmount = $grandTotalFinal; // <--- PERBAIKAN
                $finalAmount = 0;
                
            } else {
                // Alur normal, DP 50%
                $dpAmount = $grandTotalFinal * 0.5; // <--- PERBAIKAN
                $finalAmount = $grandTotalFinal * 0.5; // <--- PERBAIKAN
            }

            $booking->payments()->create([
                'amount' => $dpAmount,
                'payment_type' => 'DP',
                'status' => 'Pending',
                'proof_url' => null, 
                'verified_by' => null,
            ]);

            $booking->payments()->create([
                'payment_type' => 'Final', // (sesuai migrasi Anda)
                'amount' => $finalAmount,
                // Jika 0, status 'Verified', jika > 0, status 'Pending'
                'status' => $finalAmount > 0 ? 'Pending' : 'Verified',
                'proof_url' => null,
                'verified_by' => null,
            ]);
            

            // E. SIMPAN DETAIL ADDON
            if ($selectedAddons->isNotEmpty()) {
                foreach ($selectedAddons as $addon) {
                    $booking->bookingAddons()->create([
                        'addon_id' => $addon->id,
                        'quantity' => 1,
                        'price' => $addon->price,
                        'grand_total' => $addon->price,
                    ]);
                }
            }

            

            DB::commit();

            // REDIRECTION: Arahkan ke link tracking/checkout menggunakan order_code
            // Anda harus membuat route client.tracking dengan parameter order_code
            return redirect()->route('tracking.show', $booking->order_code) 
                             ->with('success', 'Pemesanan berhasil dibuat! Silakan cek email Anda untuk detail pelacakan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal membuat pemesanan klien: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pemesanan. Cek log error.')->withInput();
        }
    }
        /**
     * Menampilkan form untuk membuat permintaan perubahan.
     */
    public function edit($order_code)
    {
        $booking = Booking::where('order_code', $order_code)->firstOrFail();

        // 1. Muat relasi yang diperlukan
        $booking->load('package', 'payments', 'bookingAddons');

        // 2. Cek total pembayaran yang SUDAH VERIFIED (Uang masuk)
        $totalPaidVerified = $booking->payments()
                            ->where('status', 'Verified') // Pastikan status sesuai enum database Anda
                            ->sum('amount');

        // Cek apakah DP dianggap lunas (berdasarkan nominal atau status)
        // Logic: Jika ada pembayaran verified > 0, kita anggap sudah bayar DP
        $hasPaidDP = $totalPaidVerified > 0;

        // 3. Dapatkan harga paket saat ini
        $currentPrice = $booking->package->price;

        // 4. Siapkan query untuk paket yang tersedia
        $availablePackages = Package::where('is_active', true);

        // --- LOGIKA: BLOKIR DOWNGRADE ---
        // Hanya tampilkan paket dengan harga >= paket lama jika sudah bayar DP
        if ($hasPaidDP) {
            $availablePackages->where('price', '>=', $currentPrice);
        }

        $packages = $availablePackages->get();
        $addons = Addon::where('is_active', true)->get();
        $currentAddonIds = $booking->bookingAddons->pluck('addon_id')->toArray();

        return view('client.bookings.edit', compact(
            'booking', 
            'packages', 
            'addons', 
            'currentAddonIds',
            'hasPaidDP',
            'totalPaidVerified' // <--- TAMBAHKAN INI untuk JS
        ))->with('isEditMode', true);
    }
    public function update(Request $request, $order_code)
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
