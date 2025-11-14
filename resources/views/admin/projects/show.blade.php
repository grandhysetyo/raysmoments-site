@extends('layouts.admin')

@section('title', 'Verifikasi Final Payment')
@section('page-title', 'VERIFIKASI FINAL PAYMENT: ' . $booking->order_code)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-2xl border-t-4 border-indigo-600">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-6 border-b pb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-indigo-700">Verifikasi Final Payment</h1>
            <p class="text-sm text-gray-500">Order ID: {{ $booking->order_code }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-700">Status Pemesanan:</p>
            <span class="text-lg font-bold px-3 py-1 rounded-full bg-blue-100 text-blue-700">
                {{ $booking->status }}
            </span>
        </div>
    </div>
    
    {{-- Detail Klien & Acara (Boleh di-skip/copy-paste dari new-books-show) --}}
    <div class="mb-8 p-4 border rounded-lg bg-gray-50">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Detail Klien & Acara</h3>
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <div class="font-medium text-gray-600">Nama Klien:</div>
            <div class="font-bold">{{ $booking->user->clientDetails->full_name ?? 'N/A' }}</div>
            
            <div class="font-medium text-gray-600">Tanggal Acara:</div>
            <div class="font-semibold">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('dddd, D MMMM YYYY') }}</div>
        </div>
    </div>
    
    {{-- Detail Total Harga (Boleh di-skip/copy-paste dari new-books-show) --}}
    <div class="mb-8">
        <div class="text-right">
            <div class="space-y-2">
                <div class="flex justify-between text-2xl font-extrabold text-gray-900 border-t pt-2">
                    <span>GRAND TOTAL:</span>
                    <span class="text-red-600">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                </div>
                {{-- Sisa tagihan (asumsi DP 50%) --}}
                <div class="flex justify-between text-lg font-bold text-gray-700 border-t pt-2">
                    <span>Sisa Tagihan (50%):</span>
                    <span class="text-green-600">Rp {{ number_format($booking->grand_total * 0.5, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-8">
    
    {{-- BUKTI PEMBAYARAN & AKSI VERIFIKASI --}}
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Bukti Final Payment & Aksi</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        {{-- Kolom 1: Tampilan Bukti Transfer --}}
        <div>
            <h4 class="font-bold text-lg mb-3">Bukti Transfer Klien:</h4>
            
            {{-- Gunakan $finalPayment (dari controller) --}}
            @if($finalPayment && $finalPayment->proof_image_path)
                <p class="text-sm text-gray-600 mb-2">
                    Status: <strong class="text-blue-600">{{ $finalPayment->status }}</strong> | 
                    Jumlah: <strong class="text-blue-600">Rp {{ number_format($finalPayment->amount, 0) }}</strong>
                </p>
                <a href="{{ Storage::url($finalPayment->proof_image_path) }}" target="_blank" class="block border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:opacity-90 transition duration-150">
                    <img src="{{ Storage::url($finalPayment->proof_image_path) }}" alt="Bukti Transfer Final" class="w-full h-auto object-cover">
                </a>
                <p class="text-sm text-gray-500 mt-2">Klik gambar untuk melihat ukuran penuh.</p>
            @else
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <p class="font-semibold">Error: Bukti Transfer Tidak Ditemukan</p>
                </div>
            @endif
        </div>

        {{-- Kolom 2: Tombol Aksi --}}
        <div>
            <h4 class="font-bold text-lg mb-3">Aksi Verifikasi:</h4>
            <div class="space-y-4">
                
                {{-- Form 1: Konfirmasi (Status: Fully Paid) --}}
                <form action="{{ route('admin.projects.verify_final', $booking) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="verify">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                        <i class="fas fa-check-circle mr-2"></i> KONFIRMASI (LUNAS)
                    </button>
                </form>

                {{-- Form 2: Tolak Pembayaran (Status: Awaiting Final Payment) --}}
                <form action="{{ route('admin.projects.verify_final', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak bukti ini? Klien harus mengupload ulang.');">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                        <i class="fas fa-times-circle mr-2"></i> TOLAK BUKTI
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection