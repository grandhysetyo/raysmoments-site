@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran DP')
@section('page-title', 'INVOICE & VERIFIKASI: ' . $booking->order_code)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-2xl border-t-4 border-indigo-600">

    {{-- HEADER INVOICE --}}
    <div class="flex justify-between items-start mb-6 border-b pb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-indigo-700">INVOICE PEMESANAN</h1>
            <p class="text-sm text-gray-500">Order ID: {{ $booking->order_code }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-700">Status Pembayaran:</p>
            <span class="text-lg font-bold px-3 py-1 rounded-full 
                @if($booking->status == 'DP Verified') bg-green-100 text-green-700 
                @elseif($booking->status == 'Cancelled') bg-red-100 text-red-700
                @else bg-yellow-100 text-yellow-700 @endif">
                {{ $booking->status }}
            </span>
        </div>
    </div>
    
    {{-- ========================================================= --}}
    {{-- A. DATA CLIENT & ACARA --}}
    {{-- ========================================================= --}}
    <div class="mb-8 p-4 border rounded-lg bg-gray-50">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Detail Klien (Ditujukan Kepada)</h3>
        
        {{-- Group 1: Core Contact Info --}}
        <div class="grid grid-cols-2 gap-y-2 text-sm border-b border-gray-200 pb-3 mb-3">
            <div class="font-medium text-gray-600">Nama Lengkap:</div>
            <div class="font-bold">{{ $booking->user->clientDetails->full_name ?? 'N/A' }}</div>
            
            <div class="font-medium text-gray-600">Email:</div>
            <div>{{ $booking->user->email ?? 'N/A' }}</div>
            
            <div class="font-medium text-gray-600">Nomor WA:</div>
            <div>{{ $booking->user->clientDetails->whatsapp_number ?? 'N/A' }}</div>
        </div>

        {{-- Group 2: Academic Info --}}
        <div class="grid grid-cols-2 gap-y-2 text-sm border-b border-gray-200 pb-3 mb-3">
            <div class="font-medium text-gray-600">Universitas:</div>
            <div>{{ $booking->user->clientDetails->university ?? '-' }}</div>
            
            <div class="font-medium text-gray-600">Fakultas/Jurusan:</div>
            <div>{{ $booking->user->clientDetails->faculty_or_major ?? '-' }}</div>
        </div>

        {{-- Group 3: Event Info --}}
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <div class="font-medium text-gray-600">Tanggal Acara:</div>
            <div class="font-semibold">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('dddd, D MMMM YYYY') }}</div>

            <div class="font-medium text-gray-600">Lokasi Acara:</div>
            <div>{{ $booking->event_location }}</div>

            <div class="font-medium text-gray-600">Kota Acara:</div>
            <div>{{ $booking->event_city }}</div>
        </div>

        {{-- Catatan Client (Notes) --}}
        <div class="mt-4 pt-3 border-t border-gray-200">
            <p class="font-medium text-gray-600 mb-1">Catatan:</p>
            <p class="text-sm italic whitespace-pre-wrap">{{ $booking->notes ?? 'Tidak ada catatan khusus.' }}</p>
        </div>
    </div>
    {{-- END A. DATA CLIENT --}}


    {{-- ========================================================= --}}
    {{-- B. ORDER DETAILS / LINE ITEMS --}}
    {{-- ========================================================= --}}
    <h3 class="text-xl font-semibold mb-3 border-b pb-1 text-gray-800">Detail Layanan</h3>
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-indigo-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-indigo-700 uppercase tracking-wider">Harga Satuan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- ITEM 1: PAKET UTAMA --}}
                <tr class="font-semibold text-gray-900">
                    <td class="px-6 py-4 whitespace-nowrap">
                        Paket Dasar: {{ $booking->package->name ?? 'Paket Dihapus' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        Rp {{ number_format($booking->package_price, 0, ',', '.') }}
                    </td>
                </tr>
                
                {{-- ITEM 2: ADD-ONS --}}
                @forelse($booking->bookingAddons as $bookingAddon)
                    <tr class="text-gray-600">
                        <td class="px-6 py-2 whitespace-nowrap pl-10">
                            Add-on: {{ $bookingAddon->addon->name ?? 'Add-on Dihapus' }}
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-right">
                            Rp {{ number_format($bookingAddon->price, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr class="text-gray-500 italic">
                        <td class="px-6 py-2">Tidak ada Add-ons tambahan.</td>
                        <td></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- C. SUMMARY / TOTALS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- KIRI: Keterangan --}}
        <div>
            <p class="font-medium text-gray-600 mb-2">Keterangan:</p>
            <p class="text-sm italic">Informasi detail biaya dihitung di samping.</p>
        </div>
        
        {{-- KANAN: TOTAL HARGA --}}
        <div class="text-right">
            <div class="space-y-2">
                <div class="flex justify-between font-medium text-gray-700">
                    <span>Subtotal Add-ons:</span>
                    <span>Rp {{ number_format($booking->addons_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-2xl font-extrabold text-gray-900 border-t pt-2">
                    <span>GRAND TOTAL:</span>
                    <span class="text-red-600">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-700 border-t pt-2">
                    <span>DP Yang Diharapkan (50%):</span>
                    <span class="text-green-600">Rp {{ number_format($booking->grand_total * 0.5, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-8">
    
    {{-- ========================================================= --}}
    {{-- D. BUKTI PEMBAYARAN & AKSI VERIFIKASI (TOMBOL) --}}
    {{-- ========================================================= --}}
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Bukti Pembayaran & Aksi</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- Kolom 1: Tampilan Bukti Transfer --}}
        <div>
            <h4 class="font-bold text-lg mb-3">Bukti Transfer Klien:</h4>
            @if($booking->dp_proof_path) {{-- Memeriksa apakah bukti transfer sudah diunggah --}}
                <a href="{{ Storage::url($booking->dp_proof_path) }}" target="_blank" class="block border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:opacity-90 transition duration-150">
                    <img src="{{ Storage::url($booking->dp_proof_path) }}" alt="Bukti Transfer DP" class="w-full h-auto object-cover">
                </a>
                <p class="text-sm text-gray-500 mt-2">Klik gambar untuk melihat ukuran penuh.</p>
            @else
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-semibold">Belum Ada Bukti Transfer</p>
                    <p class="text-sm">Klien belum mengunggah bukti pembayaran DP.</p>
                </div>
            @endif
        </div>

        {{-- Kolom 2: Tombol Aksi --}}
        <div>
            @if($booking->status === 'Awaiting DP' || $booking->status === 'DP Submitted')
                <h4 class="font-bold text-lg mb-3">Aksi Verifikasi DP:</h4>
                <div class="space-y-4">
                    
                    {{-- Form 1: Konfirmasi DP (Status: DP Verified) --}}
                    <form action="{{ route('admin.payments.verify_dp', $booking) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="DP Verified">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200 disabled:opacity-50"
                                @if(!$booking->dp_proof_path) disabled @endif>
                            <i class="fas fa-check-circle mr-2"></i> KONFIRMASI DP
                        </button>
                        @if(!$booking->dp_proof_path)
                            <p class="text-xs text-red-500 mt-1">Tombol dinonaktifkan karena Bukti Transfer belum diunggah.</p>
                        @endif
                    </form>

                    {{-- Form 2: Tolak/Batalkan Pemesanan (Status: Cancelled) --}}
                    <form action="{{ route('admin.payments.verify_dp', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Aksi ini tidak dapat diurungkan.');">
                        @csrf
                        <input type="hidden" name="status" value="Cancelled">
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                            <i class="fas fa-times-circle mr-2"></i> TOLAK & BATALKAN PESANAN
                        </button>
                    </form>
                </div>
            @else
                <div class="p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg">
                    <p class="font-bold">Aksi Tidak Tersedia</p>
                    <p class="text-sm mt-1">Status pemesanan saat ini adalah **{{ $booking->status }}**.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection