@extends('layouts.app')

@section('content')
@php
    $dpPayment = $booking->payments->where('payment_type', 'DP')->first();
    $finalPayment = $booking->payments->where('payment_type', 'Final Payment')->first();

    // Menjumlahkan semua payment yang statusnya 'Verified'
    $totalDibayar = $booking->payments
                            ->where('status', 'Verified')
                            ->sum('amount');
                            
    // Menghitung sisa tagihan
    $sisaTagihan = $booking->grand_total - $totalDibayar;
@endphp
<div class="max-w-4xl mx-auto p-4 md:p-8 mt-6">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-900 border-b pb-3">Detail Pelacakan Pesanan</h1>
    {{-- ========================================================= --}}
    {{-- BAGIAN 1: STATUS TIMELINE --}}
    {{-- ========================================================= --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-8 border border-gray-200">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-2">Status Saat Ini:</h2>
        
        {{-- Tampilkan Peringatan jika sedang menunggu approval --}}
        @if($booking->status === 'Awaiting Change Approval')
        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
            <span class="font-medium">Menunggu Persetujuan Admin!</span> Pengajuan perubahan Anda sedang ditinjau.
        </div>
        @endif

        {{-- Timeline (Asumsi variabel $timeline dikirim dari Controller) --}}
        <ol class="relative border-l border-gray-200 dark:border-gray-700">                  
            @foreach($timeline as $step)
            <li class="mb-10 ml-6">
                {{-- BULLET POINT (Warna ditentukan oleh status) --}}
                <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -left-3 ring-8 ring-white 
                    @if($step['is_completed']) bg-green-500 @elseif($step['is_active']) bg-indigo-600 @else bg-gray-300 @endif">
                    
                    @if($step['is_completed'])
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @elseif($step['is_active'])
                        <span class="text-white text-xs font-bold">{{ $loop->iteration }}</span>
                    @endif
                </span>
                
                {{-- KONTEN TIMELINE --}}
                <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900
                    @if($step['is_active']) text-indigo-600 @endif">
                    {{ $step['label'] }}
                </h3>
                
                @if($step['is_active'])
                    <p class="mb-4 text-sm font-normal text-gray-500">
                        Status Terakhir Diperbarui: {{ $booking->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}
                    </p>
                @endif
            </li>
            @endforeach
        </ol>

        @if($booking->status === 'Cancelled')
            <div class="p-4 bg-red-100 text-red-700 border border-red-400 rounded-lg font-semibold mt-6">
                Pesanan ini telah dibatalkan.
            </div>
        @endif
    </div>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    {{-- Menampilkan error validasi (PENTING untuk modal upload) --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
            <strong class="font-bold">Oops! Ada kesalahan:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    @if($booking)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            
            {{-- HEADER KODE BOOKING --}}
            <div class="bg-gray-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Pesanan: {{ $booking->order_code }}</h2>
                        <p class="text-gray-600">Tanggal Pesan: {{ $booking->created_at->format('d F Y') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        @php
                            $statusColor = 'bg-gray-500'; // Default
                            if (in_array($booking->status, ['Awaiting DP', 'Awaiting Final Payment'])) {
                                $statusColor = 'bg-yellow-500';
                            } elseif (in_array($booking->status, ['DP Confirmed', 'On Process', 'Pending'])) {
                                $statusColor = 'bg-blue-500';
                            } elseif ($booking->status === 'Completed') {
                                $statusColor = 'bg-green-500';
                            } elseif ($booking->status === 'Cancelled') {
                                $statusColor = 'bg-red-500';
                            }
                        @endphp
                        <span class="inline-block px-3 py-1 text-sm font-semibold text-white {{ $statusColor }} rounded-full">
                            {{ $booking->status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- DAFTAR TOMBOL AKSI --}}
            <div class="p-6 border-b border-gray-200 bg-gray-50 flex flex-wrap gap-3">
                
                {{-- ========================================================== --}}
                {{-- TOMBOL UPLOAD BUKTI BAYAR (DIUBAH JADI <button>) --}}
                {{-- ========================================================== --}}
                @if($booking->status === 'Awaiting DP' || $booking->status === 'Awaiting Final Payment')
                    <button type="button" id="openPaymentModalBtn"
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        Upload Bukti Pembayaran
                    </button>
                @endif
                
                @if($booking->status !== 'Completed' && $booking->status !== 'Cancelled')
                    <button type="button" id="openChangeRequestModalBtn"
                       class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-150 ease-in-out">
                        Ajukan Perubahan Jadwal/Paket
                    </button>
                @endif
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- DETAIL KLIEN --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Detail Klien</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong class="w-24 inline-block">Nama</strong>: {{ $booking->user->clientDetails->full_name }}</p>
                        <p><strong class="w-24 inline-block">Email</strong>: {{ $booking->user->email }}</p>
                        <p><strong class="w-24 inline-block">WhatsApp</strong>: {{ $booking->user->clientDetails->whatsapp_number }}</p>
                    </div>
                </div>

                {{-- DETAIL ACARA --}}
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Detail Acara</h3>
                    <div class="space-y-2 text-sm">
                        <p><strong class="w-24 inline-block">Tanggal</strong>: {{ \Carbon\Carbon::parse($booking->event_date)->format('d F Y') }}</p>
                        <p><strong class="w-24 inline-block">Sesi 1</strong>: {{ \Carbon\Carbon::parse($booking->session_1_time)->format('H:i') }}</p>
                        @if($booking->session_2_time)
                        <p><strong class="w-24 inline-block">Sesi 2</strong>: {{ \Carbon\Carbon::parse($booking->session_2_time)->format('H:i') }}</p>
                        @endif
                        <p><strong class="w-24 inline-block">Lokasi</strong>: {{ $booking->event_location }}</p>
                        <p><strong class="w-24 inline-block">Kota</strong>: {{ $booking->event_city }}</p>
                    </div>
                </div>
            </div>

            {{-- DETAIL PAKET & ADDONS --}}
            <div class="p-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Detail Paket</h3>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700">{{ $booking->package->name }}</span>
                    <span class="font-medium text-gray-900">Rp {{ number_format($booking->package_price, 0) }}</span>
                </div>

                @if($booking->bookingAddons->count() > 0)
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2">Add-ons:</h4>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($booking->bookingAddons as $bookingAddon)
                            <li class="flex justify-between">
                                <span>{{ $bookingAddon->addon->name }}</span>
                                <span class="font-medium text-gray-800">+ Rp {{ number_format($bookingAddon->grand_total, 0) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- DETAIL PEMBAYARAN --}}
            <div class="p-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Ringkasan Pembayaran</h3>
                <div class="space-y-3">
            
                    {{-- Grand Total --}}
                    <div class="flex justify-between items-center text-gray-700">
                        <span class="text-md">Grand Total:</span>
                        <span class="text-md font-bold text-gray-900">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                    </div>
                    
                    {{-- Total Dibayar (Sudah diperbaiki) --}}
                    <div class="flex justify-between items-center text-green-600">
                        <span class="text-md">Total Dibayar:</span>
                        <span class="text-md font-bold">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                    </div>
                    
                    {{-- Sisa Tagihan (Sudah diperbaiki) --}}
                    <div class="flex justify-between items-center text-red-600 border-t pt-3">
                        <span class="text-lg font-bold">Sisa Tagihan:</span>
                        <span class="text-lg font-bold">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3">Riwayat Pembayaran:</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="text-left p-2">Tanggal</th>
                                <th class="text-left p-2">Tipe</th>
                                <th class="text-left p-2">Jumlah</th>
                                <th class="text-left p-2">Status</th>
                                <th class="text-left p-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($booking->payments as $payment)
                            <tr>
                                <td class="p-2">{{ $payment->created_at->format('d M Y') }}</td>
                                <td class="p-2">{{ $payment->payment_type }}</td>
                                <td class="p-2">Rp {{ number_format($payment->amount, 0) }}</td>
                                <td class="p-2">
                                    @php
                                        $paymentStatusColor = 'bg-gray-400';
                                        if ($payment->status === 'Pending') $paymentStatusColor = 'bg-yellow-400';
                                        if ($payment->status === 'Verified' || $payment->status === 'Paid') $paymentStatusColor = 'bg-green-400';
                                        if ($payment->status === 'Rejected') $paymentStatusColor = 'bg-red-400';
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs font-medium text-white {{ $paymentStatusColor }} rounded-full">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td class="p-2">
                                    @if($payment->proof_url)
                                        <button type="button" 
                                                class="open-proof-modal-btn text-indigo-600 hover:text-indigo-900 text-xs font-medium"
                                                {{-- Kita gunakan asset() helper, pastikan 'storage:link' sudah jalan --}}
                                                data-image-url="{{ asset('storage/' . $payment->proof_url) }}">
                                            Lihat Bukti
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-2 text-center text-gray-500">Belum ada riwayat pembayaran.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @else
        <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200 text-center">
            <h2 class="text-xl font-bold text-gray-800 mb-2">Pesanan Tidak Ditemukan</h2>
            <p class="text-gray-600">Kode pesanan yang Anda masukkan tidak valid. Silakan periksa kembali.</p>
            <a href="{{ route('tracking.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 font-medium">
                Cari lagi
            </a>
        </div>
    @endif
</div>


{{-- ========================================================== --}}
{{-- DAFTAR MODAL --}}
{{-- ========================================================== --}}
@if($booking)
    {{-- MODAL 1: Konfirmasi Pengajuan Perubahan --}}
    <div id="changeRequestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 m-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Konfirmasi Pengajuan Perubahan</h3>
            <p class="text-gray-600 mb-6">
                Anda akan diarahkan ke halaman baru untuk mengisi detail perubahan. 
                Apakah Anda yakin ingin melanjutkan?
            </p>
            <div class="flex justify-end space-x-4">
                <button type="button" id="cancelModalBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Batal
                </button>
                <a href="{{ route('request_change.show', $booking->order_code) }}"
                   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 font-medium">
                    Ya, Lanjutkan
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- MODAL 2: UPLOAD BUKTI PEMBAYARAN (BARU) --}}
    {{-- ========================================================== --}}
    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 m-4">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Upload Bukti Pembayaran</h3>
            
            {{-- Form ini akan submit ke PaymentController@store --}}
            <form action="{{ route('payments.store', $booking->order_code) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    
                    {{-- Tentukan Tipe Pembayaran (DP / Final) secara otomatis --}}
                    @php
                        $paymentType = ($booking->status === 'Awaiting DP') ? 'DP' : 'Final Payment';
                        $minAmount = ($paymentType === 'DP') ? $booking->amount : $sisaTagihan;
                    @endphp
                    <input type="hidden" name="payment_type" value="{{ $paymentType }}">

                    <div class="bg-gray-100 p-3 rounded-md text-sm">
                        Anda akan mengupload bukti untuk: <strong class="text-indigo-600">{{ $paymentType }}</strong>
                        @if($paymentType == 'DP')
                        <p>Jumlah DP: Rp {{ number_format($dpPayment->amount ?? 0, 0, ',', '.') }}</p>
                        @else
                        <p>Sisa Tagihan: Rp {{ number_format($finalPayment->amount ?? 0, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    
                    {{-- Input Jumlah --}}
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah yang Ditransfer</label>
                        <input type="number" name="amount" id="amount" 
                               value="{{ old('amount', round($minAmount)) }}" 
                               min="1000"
                               class="mt-1 w-full border border-gray-300 rounded-md p-2 @error('amount') border-red-500 @enderror" 
                               required>
                        @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    
                    {{-- Input File Gambar --}}
                    <div>
                        <label for="proof_url" class="block text-sm font-medium text-gray-700">Bukti Transfer (JPG, PNG)</label>
                        <input type="file" name="proof_url" id="proof_url" 
                               accept="image/jpeg,image/png"
                               class="mt-1 w-full border border-gray-300 rounded-md p-1 file:p-2 file:border-0 file:rounded-md file:bg-gray-100 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 @error('proof_image') border-red-500 @enderror" 
                               required>
                        @error('proof_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" id="cancelPaymentModalBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                        Upload Bukti
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- PERUBAHAN 3: Tambah Modal untuk Lihat Bukti --}}
    <div id="proofModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-4 m-4 relative">
            <button type="button" id="closeProofModalBtn" class="absolute -top-3 -right-3 bg-white rounded-full p-1 shadow-lg text-gray-700 hover:text-black z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="w-full max-h-[80vh] overflow-auto">
                <img id="proofImage" src="" alt="Bukti Pembayaran" class="w-full h-auto">
            </div>
        </div>
    </div>
@endif


{{-- ========================================================== --}}
{{-- JAVASCRIPT UNTUK KEDUA MODAL --}}
{{-- ========================================================== --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Modal 1: Konfirmasi Perubahan ---
        const openChangeBtn = document.getElementById('openChangeRequestModalBtn');
        const changeModal = document.getElementById('changeRequestModal');
        const cancelChangeBtn = document.getElementById('cancelModalBtn');

        if (openChangeBtn && changeModal && cancelChangeBtn) {
            openChangeBtn.addEventListener('click', function() {
                changeModal.style.display = 'flex';
            });
            cancelChangeBtn.addEventListener('click', function() {
                changeModal.style.display = 'none';
            });
            changeModal.addEventListener('click', function(event) {
                if (event.target === changeModal) {
                    changeModal.style.display = 'none';
                }
            });
        }

        // --- Modal 2: Upload Pembayaran (BARU) ---
        const openPaymentBtn = document.getElementById('openPaymentModalBtn');
        const paymentModal = document.getElementById('paymentModal');
        const cancelPaymentBtn = document.getElementById('cancelPaymentModalBtn');

        if (openPaymentBtn && paymentModal && cancelPaymentBtn) {
            openPaymentBtn.addEventListener('click', function() {
                paymentModal.style.display = 'flex';
            });

            cancelPaymentBtn.addEventListener('click', function() {
                paymentModal.style.display = 'none';
            });

            paymentModal.addEventListener('click', function(event) {
                if (event.target === paymentModal) {
                    paymentModal.style.display = 'none';
                }
            });
        }

        // --- PERUBAHAN 4: Tambah JS untuk Modal Bukti ---
        const proofModal = document.getElementById('proofModal');
        const proofImage = document.getElementById('proofImage');
        const closeProofBtn = document.getElementById('closeProofModalBtn');
        const openProofBtns = document.querySelectorAll('.open-proof-modal-btn');

        if (proofModal && proofImage && closeProofBtn && openProofBtns) {
            
            // Logika untuk tombol "Lihat Bukti" (ada banyak)
            openProofBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const imageUrl = btn.dataset.imageUrl;
                    if (imageUrl) {
                        proofImage.src = imageUrl; // Set gambar di modal
                        proofModal.style.display = 'flex'; // Tampilkan modal
                    }
                });
            });

            // Logika tutup modal
            const closeProofModal = () => {
                proofModal.style.display = 'none';
                proofImage.src = ''; // Kosongkan gambar saat ditutup
            };

            closeProofBtn.addEventListener('click', closeProofModal);
            proofModal.addEventListener('click', (event) => {
                // Hanya tutup jika klik di overlay (latar belakang), bukan di gambar/konten
                if (event.target === proofModal) {
                    closeProofModal();
                }
            });
        }

        // Jika ada error validasi, otomatis tampilkan modal payment
        @if ($errors->has('amount') || $errors->has('proof_url'))
            if (paymentModal) {
                paymentModal.style.display = 'flex';
            }
        @endif

    });
</script>

@endsection