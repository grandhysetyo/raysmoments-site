<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingChangeRequest;
use App\Models\BookingAddon;
use App\Models\Addon;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChangeRequestController extends Controller
{
    /**
     * Menampilkan daftar permintaan perubahan yang Pending.
     */
    public function index()
    {
        // Ambil request yang pending, urutkan dari yang terlama (First In First Out)
        $requests = BookingChangeRequest::with(['booking.user.clientDetails', 'newPackage'])
                                        ->where('status', 'Pending')
                                        ->oldest() // Prioritaskan yang masuk duluan
                                        ->paginate(10);

        return view('admin.change_requests.index', compact('requests'));
    }

    /**
     * Menampilkan detail permintaan perubahan.
     */
    public function show($id)
    {
        $request = BookingChangeRequest::with(['booking.user.clientDetails', 'booking.package', 'newPackage'])
                                       ->findOrFail($id);

        return view('admin.change_requests.show', compact('request'));
    }

    /**
     * Menyetujui permintaan perubahan (Memindahkan logika dari diskusi sebelumnya kesini).
     */
    public function approve(Request $request, $id)
    {
        $changeRequest = BookingChangeRequest::with('booking', 'newPackage')->findOrFail($id);
        $booking = $changeRequest->booking;

        if ($changeRequest->status !== 'Pending') {
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // =========================================================
            // 1. UPDATE DATA BOOKING (TANGGAL, LOKASI, PAKET, ADDONS)
            // =========================================================
            
            // A. Update Info Dasar (Sama seperti sebelumnya)
            $booking->event_date = $changeRequest->new_event_date;
            $booking->event_location = $changeRequest->new_event_location;
            $booking->event_city = $changeRequest->new_event_city;

            // B. Update Paket (DIUBAH: Update juga package_price jika ada)
            if ($changeRequest->new_package_id) {
                $booking->package_id = $changeRequest->new_package_id;
                // Update harga paket di booking agar perhitungan grand_total akurat
                if ($changeRequest->newPackage) {
                    $booking->package_price = $changeRequest->newPackage->price;
                }
            }

            // C. Update Add-ons (BAGIAN BARU: Hapus Lama -> Insert Baru)
            // Cek apakah request ini membawa data perubahan add-ons
            if (!is_null($changeRequest->new_addons)) {
            
                // 1. Hapus semua add-ons lama milik booking ini
                BookingAddon::where('booking_id', $booking->id)->delete();
    
                // 2. Masukkan add-ons baru
                $newAddonIds = $changeRequest->new_addons; 
    
                // --- FIX: Pastikan datanya Array (jika Model cast tidak jalan/data lama) ---
                if (is_string($newAddonIds)) {
                    $newAddonIds = json_decode($newAddonIds, true);
                }
                // Pastikan hasil decode adalah array (bukan null)
                if (!is_array($newAddonIds)) {
                    $newAddonIds = [];
                }
                // --------------------------------------------------------------------------
    
                if (!empty($newAddonIds)) {
                    // Ambil data harga asli addon dari master data
                    $addons = Addon::whereIn('id', $newAddonIds)->get(); // Error terjadi disini sebelumnya
                    
                    foreach ($addons as $addon) {
                        BookingAddon::create([
                            'booking_id' => $booking->id,
                            'addon_id'   => $addon->id,
                            'price'      => $addon->price,
                        ]);
                    }
                }
            }

            // D. Hitung Ulang Grand Total (BAGIAN BARU: Sangat Penting!)
            // Kita harus hitung ulang karena Paket atau Add-ons mungkin berubah
            $newAddonsTotal = $booking->bookingAddons()->sum('price');
            $booking->grand_total = $booking->package_price + $newAddonsTotal;
            $booking->addons_total = $newAddonsTotal; // Jika Anda punya kolom ini

            // Catat Notes
            $booking->notes = $booking->notes . "\n[Perubahan disetujui]: " . $changeRequest->reason;
            $booking->save();


            // =========================================================
            // 2. LOGIKA KEUANGAN (PERBAIKAN DISINI)
            // =========================================================
            $totalPaidVerified = $booking->payments()->where('status', 'Verified')->sum('amount');

            if ($totalPaidVerified == 0) {
                // SKENARIO A: Belum Bayar DP sama sekali (Status: Awaiting DP)
                // Sistem awal sudah generate 2 tagihan: DP (50%) & Final (50%).
                // Kita harus update KEDUA tagihan tersebut dengan harga baru.

                $newTotal = $booking->grand_total;
                $newDP    = $newTotal * 0.50;          // 50%
                $newFinal = $newTotal - $newDP;        // Sisa 50% (Hitung sisa agar akurat jika ganjil)

                // 1. Update Tagihan DP yang masih Pending
                $booking->payments()
                        ->where('payment_type', 'DP')
                        ->where('status', 'Pending')
                        ->update(['amount' => $newDP]);

                // 2. Update Tagihan Final yang masih Pending (FIX BUG INI)
                $booking->payments()
                        ->where('payment_type', 'Final')
                        ->where('status', 'Pending')
                        ->update(['amount' => $newFinal]);
                
                // Opsional: Jika Anda menyimpan sisa tagihan di table booking
                // $booking->final_payment_amount = $newFinal;
                // $booking->save();

            } elseif ($changeRequest->additional_cost > 0) {
                
                // SKENARIO B: Sudah Bayar DP -> Buat Tagihan Tambahan (Extra)
                // ... (Logika ini tetap sama, tidak perlu diubah) ...
                
                Payment::create([
                    'booking_id'   => $booking->id,
                    'payment_type' => 'Extra', // Pastikan enum 'Extra' ada di database
                    'amount'       => $changeRequest->additional_cost,
                    'status'       => 'Pending',
                    'description'  => 'Biaya tambahan perubahan Paket/Jadwal #' . $changeRequest->id,
                ]);
            }

            // 3. Selesai (Sama seperti sebelumnya)
            $changeRequest->status = 'Approved';
            $changeRequest->admin_notes = $request->input('admin_notes', 'Disetujui.');
            $changeRequest->save();

            DB::commit();

            return redirect()->route('admin.change-requests.index')
                                ->with('success', 'Permintaan perubahan berhasil disetujui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Menolak permintaan perubahan.
     */
    public function reject(Request $request, $id)
    {
        $changeRequest = BookingChangeRequest::findOrFail($id);
        
        $request->validate(['admin_notes' => 'required|string']);

        $changeRequest->update([
            'status' => 'Rejected',
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->route('admin.change-requests.index')
                         ->with('success', 'Permintaan perubahan ditolak.');
    }
}