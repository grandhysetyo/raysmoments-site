<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Addon;
use Illuminate\Support\Str;
use App\Models\User;


class BookingController extends Controller
{
  
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        $addons = Addon::where('is_active', true)->get();
        return view('client.bookings.create', compact('packages','addons'));
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
            'event_location' => 'required|string|max:255',
            'event_city' => 'required|string|max:255',
            
            // --- Kalkulasi Harga (dari form) ---
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id',
            'grand_total' => 'required|numeric|min:0', // Nilai dari JS
            'package_price_hidden' => 'required|numeric|min:0',
        ]);
        
        // 1. Ambil data Addons untuk kalkulasi di backend
        $selectedAddons = Addon::whereIn('id', $validatedData['addons'] ?? [])->get();
        $package = Package::findOrFail($validatedData['package_id']);

        $packagePrice = $package->price;
        $addonsTotal = $selectedAddons->sum('price');
        $grandTotalFinal = $packagePrice + $addonsTotal;
        $dpAmountFinal = $grandTotalFinal * 0.5; // Hitung DP 50%

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
                'event_location' => $validatedData['event_location'],
                'event_city' => $validatedData['event_city'],
                'status' => 'Awaiting DP',
            ]);

            // D. BUAT PAYMENT (Record DP yang belum dibayarkan)
            $booking->payments()->create([
                'amount' => $dpAmountFinal,
                'payment_type' => 'DP',
                'status' => 'Pending',
            ]);

            // E. SIMPAN DETAIL ADDON
            if ($selectedAddons->isNotEmpty()) {
                foreach ($selectedAddons as $addon) {
                    $booking->bookingAddons()->create([
                        'addon_id' => $addon->id,
                        'quantity' => 1,
                        'price' => $addon->price,
                        'total_price' => $addon->price,
                    ]);
                }
            }

            DB::commit();

            // REDIRECTION: Arahkan ke link tracking/checkout menggunakan order_code
            // Anda harus membuat route client.tracking dengan parameter order_code
            return redirect()->route('client.tracking', $booking->order_code) 
                             ->with('success', 'Pemesanan berhasil dibuat! Silakan cek email Anda untuk detail pelacakan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal membuat pemesanan klien: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuat pemesanan. Cek log error.')->withInput();
        }
    }

    public function show(Booking $booking)
    {
        $booking->load('package','addons','payments');
        return view('client.bookings.show', compact('booking'));
    }

    // Form tracking status
    public function trackForm()
    {
        return view('client.bookings.track');
    }

    // Tracking by order_code
    public function track(Request $request)
    {
        $request->validate(['order_code'=>'required|string']);
        $booking = Booking::where('order_code',$request->order_code)->first();
        return view('client.bookings.track_result', compact('booking'));
    }
}
