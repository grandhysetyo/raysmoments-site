@extends('layouts.app')

@section('content')
{{-- CONTAINER: Gunakan max-w-lg agar tampilan fokus seperti Mobile App meskipun di Desktop --}}
<div class="max-w-lg mx-auto p-4 pb-24">
    
    {{-- 1. HEADER & STATUS UTAMA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-indigo-600"></div>
        
        <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Kode Pesanan</p>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">{{ $booking->order_code }}</h1>
        
        {{-- Badge Status Utama --}}
        <div class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold 
            @if($booking->status == 'Cancelled') bg-red-100 text-red-700
            @elseif(in_array($booking->status, ['Fully Paid', 'Shooting Completed', 'Project Closed'])) bg-green-100 text-green-700
            @else bg-indigo-50 text-indigo-700 border border-indigo-100 @endif">
            
            <span class="relative flex h-2 w-2 mr-2">
              @if(!in_array($booking->status, ['Cancelled', 'Project Closed']))
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-current"></span>
              @endif
              <span class="relative inline-flex rounded-full h-2 w-2 bg-current"></span>
            </span>
            {{ $booking->status }}
        </div>
        <p class="text-xs text-gray-400 mt-3">Dipesan: {{ $booking->created_at->format('d M Y') }}</p>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r mb-6 shadow-sm text-sm">
            <p class="font-bold">Berhasil!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r mb-6 shadow-sm text-sm">
            <p class="font-bold">Error!</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- LOGIKA PENENTUAN FORM (PHP Logic) --}}
    @php
        $isUpgradeApproved = isset($latestChangeRequest) && $latestChangeRequest->status == 'Approved';
        $hasPendingUpgradePayment = $booking->payments->where('payment_type', 'AddOn')->where('amount', $latestChangeRequest->additional_cost ?? 0)->where('status', 'Pending')->isNotEmpty();
        $paid = $booking->payments->where('status', 'Verified')->sum('amount');
        $bill = $booking->grand_total - $paid;
        $status = $booking->status;
        $dpAmount = $booking->grand_total * 0.5;
    @endphp


    {{-- 2. ACTION AREA (Form Pembayaran) - Prioritas Tertinggi --}}
    
    {{-- A. FORM UPGRADE --}}
    @if($isUpgradeApproved && $latestChangeRequest->additional_cost > 0 && $bill > 0 && !$hasPendingUpgradePayment)
        <div class="bg-white rounded-2xl shadow-lg border border-green-200 overflow-hidden mb-6 relative transform transition hover:scale-[1.01]">
            <div class="bg-green-500 text-white px-4 py-2 text-xs font-bold uppercase tracking-wider flex justify-between items-center">
                <span>Perubahan Disetujui</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="p-6">
                <h3 class="font-bold text-gray-900 mb-2">Bayar Kekurangan Upgrade</h3>
                <p class="text-gray-500 text-sm mb-4">Upgrade paket Anda telah disetujui admin.</p>
                
                <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg border border-green-100 mb-4">
                    <span class="text-sm text-gray-600">Nominal</span>
                    <span class="font-bold text-lg text-green-700">Rp {{ number_format($latestChangeRequest->additional_cost, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('payments.store', $booking->order_code) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_type" value="AddOn"> 
                    <input type="hidden" name="amount" value="{{ $latestChangeRequest->additional_cost }}">
                    <input type="hidden" name="change_request_id" value="{{ $latestChangeRequest->id }}">
                    
                    <label class="block mb-4">
                        <span class="text-xs font-bold text-gray-700 mb-1 block">Upload Bukti Transfer</span>
                        <input type="file" name="proof_url" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-lg" required>
                    </label>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-md hover:bg-indigo-700 transition">
                        Kirim Bukti
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- B. FORM DP --}}
    @if($status == 'Awaiting DP')
        <div class="bg-white rounded-2xl shadow-lg border border-indigo-100 overflow-hidden mb-6 relative">
            <div class="bg-indigo-600 text-white px-4 py-2 text-xs font-bold uppercase tracking-wider flex justify-between items-center">
                <span>Tagihan Aktif</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="p-6">
                <h3 class="font-bold text-gray-900 mb-2">Pembayaran DP (50%)</h3>
                <p class="text-gray-500 text-sm mb-4">Amankan tanggal acara Anda sekarang.</p>
                
                <div class="flex justify-between items-center bg-indigo-50 p-3 rounded-lg border border-indigo-100 mb-4">
                    <span class="text-sm text-gray-600">Total DP</span>
                    <span class="font-bold text-lg text-indigo-700">Rp {{ number_format($dpAmount, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('payments.store', $booking->order_code) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_type" value="DP"> 
                    <input type="hidden" name="amount" value="{{ $dpAmount }}">
                    
                    <label class="block mb-4">
                        <span class="text-xs font-bold text-gray-700 mb-1 block">Upload Bukti Transfer</span>
                        <input type="file" name="proof_url" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-lg" required>
                    </label>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-bold shadow-md hover:bg-indigo-700 transition">
                        Bayar DP Sekarang
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- C. FORM PELUNASAN --}}
    @if($status == 'Awaiting Final Payment')
        <div class="bg-white rounded-2xl shadow-lg border border-blue-200 overflow-hidden mb-6 relative">
            <div class="bg-blue-600 text-white px-4 py-2 text-xs font-bold uppercase tracking-wider flex justify-between items-center">
                <span>Tahap Akhir</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="p-6">
                <h3 class="font-bold text-gray-900 mb-2">Pelunasan Tagihan</h3>
                <p class="text-gray-500 text-sm mb-4">Selesaikan pembayaran untuk memproses foto.</p>
                
                <div class="flex justify-between items-center bg-blue-50 p-3 rounded-lg border border-blue-100 mb-4">
                    <span class="text-sm text-gray-600">Sisa Tagihan</span>
                    <span class="font-bold text-lg text-blue-700">Rp {{ number_format($bill, 0, ',', '.') }}</span>
                </div>

                <form action="{{ route('payments.store', $booking->order_code) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_type" value="Final"> 
                    <input type="hidden" name="amount" value="{{ $bill }}">
                    
                    <label class="block mb-4">
                        <span class="text-xs font-bold text-gray-700 mb-1 block">Upload Bukti Transfer</span>
                        <input type="file" name="proof_url" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-lg" required>
                    </label>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-bold shadow-md hover:bg-blue-700 transition">
                        Lunasi Sekarang
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- D. ALERT SEDANG DIVERIFIKASI --}}
    @if($hasPendingUpgradePayment || in_array($status, ['Pending', 'Pending Final Payment']))
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6 flex items-start shadow-sm">
            <div class="flex-shrink-0 mt-1">
                 <svg class="h-5 w-5 text-yellow-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-yellow-800">Pembayaran Sedang Diverifikasi</h3>
                <p class="text-xs text-yellow-600 mt-1">Admin sedang mengecek bukti transfer Anda. Mohon tunggu notifikasi selanjutnya.</p>
            </div>
        </div>
    @endif


    {{-- 3. TIMELINE (Vertical Minimalis) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Progres Pesanan</h3>
        <div class="relative pl-2">
            <div class="absolute left-3.5 top-2 h-[90%] w-0.5 bg-gray-100"></div>
            <ul class="space-y-5">
                @foreach($timeline as $step)
                <li class="relative flex items-center">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full border-2 border-white shadow-sm z-10 
                        {{ $step['is_completed'] ? 'bg-green-500' : ($step['is_active'] ? 'bg-indigo-600 ring-4 ring-indigo-50' : 'bg-gray-300') }}">
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-bold {{ $step['is_active'] ? 'text-indigo-700' : ($step['is_completed'] ? 'text-gray-800' : 'text-gray-400') }}">
                            {{ $step['label'] }}
                        </p>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>


    {{-- 4. ACCORDION DETAILS (Fitur Kunci UX Mobile) --}}
    {{-- Menggunakan HTML5 <details> untuk native accordion yang ringan --}}
    
    <div class="space-y-3">
        {{-- Detail 1: Informasi Pemesan --}}
        <details class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <summary class="flex justify-between items-center p-4 cursor-pointer bg-gray-50 group-open:bg-white transition list-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-bold text-gray-700 text-sm">Data Pemesan</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-4 border-t border-gray-100 text-sm space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Nama</p>
                    <p class="font-medium text-gray-900">{{ $booking->user->clientDetails->full_name ?? $booking->user->name }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">WhatsApp</p>
                        <p class="font-medium text-gray-900">{{ $booking->user->clientDetails->whatsapp_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Instagram</p>
                        <p class="font-medium text-gray-900">{{ $booking->user->clientDetails->instagram ? '@'.$booking->user->clientDetails->instagram : '-' }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Email</p>
                    <p class="font-medium text-gray-900 truncate">{{ $booking->user->email }}</p>
                </div>
            </div>
        </details>

        {{-- Detail 2: Riwayat Pembayaran --}}
        <details class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <summary class="flex justify-between items-center p-4 cursor-pointer bg-gray-50 group-open:bg-white transition list-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-bold text-gray-700 text-sm">Riwayat Pembayaran</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-4 border-t border-gray-100">
                @if($booking->payments->isEmpty())
                    <p class="text-gray-400 text-center text-xs py-2">Belum ada data pembayaran.</p>
                @else
                    <div class="space-y-3">
                        @foreach($booking->payments as $payment)
                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                            <div>
                                <p class="text-xs font-bold text-gray-700">
                                    {{ $payment->payment_type == 'AddOn' ? 'Upgrade' : $payment->payment_type }}
                                </p>
                                <p class="text-[10px] text-gray-500">{{ $payment->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                <div class="flex items-center justify-end mt-1 space-x-2">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full 
                                        {{ $payment->status == 'Verified' ? 'bg-green-100 text-green-700' : ($payment->status == 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                        {{ $payment->status }}
                                    </span>
                                    @if($payment->proof_url)
                                        <button onclick="openProofModal('{{ asset('storage/' . $payment->proof_url) }}')" class="text-[10px] font-bold text-indigo-600 underline">Lihat</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </details>

        {{-- Detail 3: Informasi Paket & Biaya --}}
        <details class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" open>
            <summary class="flex justify-between items-center p-4 cursor-pointer bg-gray-50 group-open:bg-white transition list-none">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="font-bold text-gray-700 text-sm">Detail Paket</span>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </summary>
            <div class="p-4 border-t border-gray-100 text-sm space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Paket</span>
                    <span class="font-bold text-indigo-700">{{ $booking->package->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Lokasi</span>
                    <span class="text-gray-900 text-right max-w-[60%]">{{ $booking->event_location }}, {{ $booking->event_city }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="text-gray-900">{{ $booking->event_date->format('d M Y') }}</span>
                </div>
                
                <div class="border-t border-dashed border-gray-200 pt-3 mt-2">
                    <div class="flex justify-between text-gray-600 text-xs mb-1">
                        <span>Harga Paket</span>
                        <span>Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span>
                    </div>
                    @if($booking->addons_total > 0)
                    <div class="flex justify-between text-gray-600 text-xs mb-1">
                        <span>Add-ons</span>
                        <span>+ Rp {{ number_format($booking->addons_total, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-gray-900 text-base mt-2">
                        <span>Grand Total</span>
                        <span>Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</span>
                    </div>
                    
                    {{-- Status Bayar Mini --}}
                    <div class="mt-3 bg-gray-50 rounded p-2 text-xs space-y-1">
                         @if($paid > 0)
                            <div class="flex justify-between text-green-600"><span>Sudah Bayar</span><span>- Rp {{ number_format($paid, 0, ',', '.') }}</span></div>
                         @endif
                         @if($bill > 0)
                            <div class="flex justify-between text-red-600 font-bold"><span>Sisa Tagihan</span><span>Rp {{ number_format($bill, 0, ',', '.') }}</span></div>
                         @else
                            <div class="text-center text-green-600 font-bold">LUNAS</div>
                         @endif
                    </div>
                </div>
                
                @if(!in_array($booking->status, ['Shooting Completed', 'Project Closed', 'Cancelled']))
                    <div class="pt-2 text-center">
                        <a href="{{ route('edit.show', $booking->order_code) }}" class="text-indigo-600 text-xs font-bold hover:underline">
                            Ajukan Perubahan Paket?
                        </a>
                    </div>
                @endif
            </div>
        </details>
    </div>

</div>

{{-- MODAL LIHAT BUKTI --}}
<div id="proofModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-black bg-opacity-80 transition-opacity backdrop-blur-sm" onclick="closeProofModal()"></div>
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl max-w-sm w-full transform transition-all">
            <div class="p-2 flex justify-end">
                <button onclick="closeProofModal()" class="bg-gray-100 rounded-full p-1 hover:bg-gray-200 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-4 pt-0 text-center">
                <img id="proofImage" src="" alt="Bukti" class="w-full h-auto rounded-lg shadow-sm border border-gray-100">
                <p class="text-xs text-gray-400 mt-3">Bukti Transfer</p>
            </div>
        </div>
    </div>
</div>

<script>
    function openProofModal(imageUrl) {
        const modal = document.getElementById('proofModal');
        const img = document.getElementById('proofImage');
        img.src = imageUrl;
        modal.classList.remove('hidden');
    }
    function closeProofModal() {
        document.getElementById('proofModal').classList.add('hidden');
    }
</script>
@endsection