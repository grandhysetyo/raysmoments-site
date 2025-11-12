@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4 md:p-8 mt-6">
    <h1 class="text-3xl font-extrabold mb-2 text-gray-900">🔍 Pelacakan Status Pesanan</h1>
    <p class="text-gray-600 mb-6">Order ID: <span class="font-mono font-semibold text-indigo-600">{{ $booking->order_code }}</span></p>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

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

    {{-- ========================================================= --}}
    {{-- BAGIAN 2: TOMBOL AJUKAN PERUBAHAN (DENGAN LOGIKA H-1) --}}
    {{-- ========================================================= --}}
    <div class="bg-white p-6 rounded-xl shadow-lg mb-8 border border-gray-200">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">Ajukan Perubahan</h2>
        
        @php
            // Tentukan apakah update bisa dilakukan (H-1)
            $eventDate = \Carbon\Carbon::parse($booking->event_date)->startOfDay();
            // 'Tomorrow' (Besok) dihitung dari jam 00:00
            $tomorrow = \Carbon\Carbon::now()->addDay(1)->startOfDay(); 
            
            // $isUpdateDisabled = true jika $eventDate <= $tomorrow (Acara hari ini atau besok)
            $isUpdateDisabled = $eventDate->lte($tomorrow); 
        @endphp

        @if($booking->status === 'Awaiting Change Approval')
            <p class="text-gray-600 italic">Anda tidak dapat mengajukan perubahan baru saat satu pengajuan sedang ditinjau.</p>
        
        @elseif($isUpdateDisabled)
            <p class="text-gray-600 italic">Perubahan jadwal tidak dapat diajukan H-1 atau pada hari acara.</p>
            <button class="px-6 py-2 bg-gray-400 text-white rounded-lg shadow cursor-not-allowed" disabled>
                Ajukan Perubahan (Ditutup)
            </button>

        @else
            <p class="text-gray-600 mb-4">Perubahan pada Tanggal, Lokasi, atau Kota memerlukan persetujuan manual dari Admin untuk memastikan ketersediaan tim.</p>
            <button id="openChangeModalBtn" class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition duration-200">
                Ajukan Perubahan Jadwal/Lokasi
            </button>
        @endif
    </div>


    {{-- ========================================================= --}}
    {{-- BAGIAN 3: DETAIL PESANAN & HARGA --}}
    {{-- ========================================================= --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">Rincian Pemesanan</h2>
        
        {{-- Data Klien & Acara --}}
        <div class="mb-6 p-4 border rounded-lg bg-gray-50">
            <h3 class="text-lg font-semibold mb-3 text-gray-800">Detail Klien & Acara</h3>
            
            <div class="grid grid-cols-2 gap-y-2 text-sm">
                <div class="font-medium text-gray-600">Nama Lengkap:</div>
                <div class="font-bold">{{ $booking->user->clientDetails->full_name ?? 'N/A' }}</div>
                
                <div class="font-medium text-gray-600">Email:</div>
                <div>{{ $booking->user->email ?? 'N/A' }}</div>
                
                <div class="font-medium text-gray-600">Tanggal Acara:</div>
                <div class="font-semibold">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('dddd, D MMMM YYYY') }}</div>

                <div class="font-medium text-gray-600">Lokasi Acara:</div>
                <div>{{ $booking->event_location }} ({{ $booking->event_city }})</div>
            </div>
        </div>

        {{-- Detail Paket & Addons --}}
        <h3 class="text-xl font-semibold mb-3 border-b pb-1 text-gray-800">Rincian Harga</h3>
        <div class="overflow-x-auto mb-8">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-600 uppercase tracking-wider">Harga</th>
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

        {{-- TOTALS --}}
        <div class="flex justify-end">
            <div class="w-full md:w-1/2 text-right space-y-3">
                <div class="flex justify-between font-medium text-gray-700">
                    <span>Total Add-ons:</span>
                    <span>Rp {{ number_format($booking->addons_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-2xl font-extrabold text-gray-900 border-t pt-3">
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
</div>


{{-- ========================================================= --}}
{{-- MODAL FORM PENGAJUAN PERUBAHAN --}}
{{-- ========================================================= --}}
<div id="changeRequestModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 m-4">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="text-2xl font-bold text-gray-800">Formulir Pengajuan Perubahan</h3>
            <button id="closeChangeModalBtn" class="text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
        </div>
        
        <form action="{{ route('client.bookings.request_change', $booking) }}" method="POST">
            @csrf
            <div class="space-y-4">
                
                {{-- Data Lama (Read-only) --}}
                <div class="bg-gray-100 p-3 rounded-md border border-gray-200">
                    <p class="text-sm font-semibold text-gray-700">Jadwal Saat Ini:</p>
                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMMM YYYY') }} - {{ $booking->event_location }} ({{ $booking->event_city }})</p>
                </div>

                {{-- Data Baru (Input) --}}
                <h4 class="text-lg font-semibold text-gray-700">Jadwal Baru Yang Diajukan:</h4>
                
                <div>
                    <label for="new_event_date" class="block text-sm font-medium text-gray-700">Tanggal Acara Baru</label>
                    <input type="date" name="new_event_date" id="new_event_date" class="w-full border border-gray-300 rounded p-2" required>
                    @error('new_event_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="new_event_location" class="block text-sm font-medium text-gray-700">Lokasi Baru (Nama Tempat)</label>
                    <input type="text" name="new_event_location" id="new_event_location" class="w-full border border-gray-300 rounded p-2" required>
                    @error('new_event_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="new_event_city" class="block text-sm font-medium text-gray-700">Kota Baru</label>
                    <input type="text" name="new_event_city" id="new_event_city" class="w-full border border-gray-300 rounded p-2" required>
                    @error('new_event_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Alasan Perubahan</label>
                    <textarea name="reason" id="reason" rows="3" class="w-full border border-gray-300 rounded p-2" placeholder="Misal: Jadwal wisuda diundur..." required></textarea>
                    @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end pt-4 border-t">
                    <button type="button" id="cancelChangeModalBtn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg mr-2 hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Kirim Pengajuan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('changeRequestModal');
    const openBtn = document.getElementById('openChangeModalBtn');
    const closeBtn = document.getElementById('closeChangeModalBtn');
    const cancelBtn = document.getElementById('cancelChangeModalBtn');

    if (openBtn) {
        openBtn.addEventListener('click', () => modal.classList.remove('hidden'));
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
    }
    
    // Klik di luar modal untuk menutup
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection