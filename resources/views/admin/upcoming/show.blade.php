@extends('layouts.admin')

@section('title', 'Assign Fotografer')
@section('page-title', 'Tugaskan Fotografer: ' . $booking->order_code)

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-2xl border-t-4 border-indigo-600">

    {{-- Detail Singkat Booking --}}
    <div class="mb-6 p-4 border rounded-lg bg-gray-50">
        <h3 class="text-lg font-semibold mb-3 text-gray-800">Detail Job</h3>
        <div class="grid grid-cols-2 gap-y-2 text-sm">
            <div class="font-medium text-gray-600">Klien:</div>
            <div class="font-bold">{{ $booking->user->clientDetails->full_name ?? $booking->user->name }}</div>
            
            <div class="font-medium text-gray-600">Tanggal Acara:</div>
            <div class="font-semibold">{{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('dddd, D MMMM YYYY') }}</div>
            
            <div class="font-medium text-gray-600">Lokasi:</div>
            <div>{{ $booking->event_location }}, {{ $booking->event_city }}</div>
            
            <div class="font-medium text-gray-600">Paket:</div>
            <div>{{ $booking->package->name }}</div>
        </div>
    </div>

    <hr class="my-6">

    {{-- ========================================================= --}}
    {{-- FORM ASSIGNMENT (INI BERUBAH) --}}
    {{-- ========================================================= --}}
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Form Penugasan</h3>
    <form action="{{ route('admin.upcoming.assign', $booking) }}" method="POST">
        @csrf
        <div class="space-y-4">
            
            {{-- 1. Pilih Fotografer --}}
            <div>
                <label for="photographer_id" class="block text-sm font-medium text-gray-700">Pilih Fotografer</label>
                <select name="photographer_id" id="photographer_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                    <option value="">-- Pilih Fotografer --</option>
                    @foreach($photographers as $photographer)
                        <option value="{{ $photographer->id }}" 
                                @selected(old('photographer_id', $booking->photographer_id) == $photographer->id)>
                            {{ $photographer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- 2. Pilih Rate (Dinamis, HANYA untuk membantu) --}}
            <div>
                <label for="rate_selector" class="block text-sm font-medium text-gray-700">Pilih Tarif (Otomatis)</label>
                <select id="rate_selector" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm bg-gray-100" disabled>
                    <option value="">-- Pilih fotografer terlebih dahulu --</option>
                    {{-- Opsi rate akan diisi oleh JavaScript --}}
                </select>
                <p class="text-xs text-gray-500 mt-1">Ini hanya alat bantu, harga akan disalin ke bawah.</p>
            </div>

            {{-- 3. Input Rate (INI YANG DI-SUBMIT) --}}
            <div>
                <label for="photographer_rate" class="block text-sm font-medium text-gray-700">Fee Fotografer (Rp)</label>
                <input type="number" name="photographer_rate" id="photographer_rate" 
                       value="{{ old('photographer_rate', $booking->photographer_rate) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm @error('photographer_rate') border-red-500 @enderror" 
                       required min="0" step="1000">
                @error('photographer_rate')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="photographer_other_costs" class="block text-sm font-medium text-gray-700">Biaya Lain-Lain (Rp)</label>
                <input type="number" name="photographer_other_costs" id="photographer_other_costs" 
                       value="{{ old('photographer_other_costs', $booking->photographer_other_costs) }}"
                       class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm @error('photographer_other_costs') border-red-500 @enderror" 
                       min="0" step="1000" placeholder="e.g., 50000">
                <p class="text-xs text-gray-500 mt-1">Opsional. Untuk transport, akomodasi, dll.</p>
                @error('photographer_other_costs')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

        </div>
        
        <div class="mt-6">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                {{ $booking->photographer_id ? 'Update Penugasan' : 'Tugaskan Fotografer' }}
            </button>
        </div>
    </form>
    
    <hr class="my-8">

   {{-- Aksi Refund --}}
   <form action="{{ route('admin.upcoming.refund', $booking) }}" method="POST" onsubmit="return confirm('PERINGATAN: Aksi ini akan membatalkan pesanan dan menandai DP sebagai Refund. Anda yakin?');">
       @csrf
       <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
           Batalkan & Refund Pesanan
       </button>
   </form>
</div>

{{-- ========================================================= --}}
{{-- SCRIPT BARU (INI BERUBAH) --}}
{{-- ========================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photographerSelect = document.getElementById('photographer_id');
    const rateSelector = document.getElementById('rate_selector');
    const rateInput = document.getElementById('photographer_rate');
    
    // Fungsi untuk mengisi dropdown rate
    function populateRates(rates) {
        rateSelector.innerHTML = '<option value="">-- Pilih Tarif --</option>'; // Reset
        if (rates.length > 0) {
            rates.forEach(rate => {
                const option = document.createElement('option');
                option.value = rate.price; // value-nya adalah harganya
                option.textContent = `${rate.rate_name} (Rp ${new Intl.NumberFormat().format(rate.price)})`;
                rateSelector.appendChild(option);
            });
            rateSelector.disabled = false;
        } else {
            rateSelector.innerHTML = '<option value="">-- Fotografer ini tidak memiliki tarif --</option>';
            rateSelector.disabled = true;
        }
    }

    // Fungsi untuk fetch rate saat ganti fotografer
    function fetchRates(photographerId) {
        rateSelector.disabled = true;
        rateSelector.innerHTML = '<option value="">Loading...</option>';

        if (photographerId) {
            // Panggil route helper kita
            fetch(`/admin/upcoming/get-rates/${photographerId}`) // Pastikan URL ini benar
                .then(response => response.json())
                .then(rates => {
                    populateRates(rates);
                })
                .catch(error => {
                    console.error('Error fetching rates:', error);
                    rateSelector.innerHTML = '<option value="">Gagal memuat tarif</option>';
                });
        } else {
            rateSelector.innerHTML = '<option value="">-- Pilih fotografer terlebih dahulu --</option>';
        }
    }

    // --- EVENT LISTENERS ---

    // 1. Saat ganti fotografer
    photographerSelect.addEventListener('change', function () {
        const photographerId = this.value;
        rateInput.value = ''; // Kosongkan harga
        fetchRates(photographerId);
    });

    // 2. Saat memilih rate dari dropdown bantu
    rateSelector.addEventListener('change', function () {
        const selectedPrice = this.value;
        if (selectedPrice) {
            rateInput.value = selectedPrice; // Salin harga ke input rate
        }
    });

    // 3. Panggil saat halaman di-load (jika fotografer sudah dipilih)
    if (photographerSelect.value) {
        fetchRates(photographerSelect.value);
        // (Kita tidak auto-select rate, tapi input 'photographer_rate' sudah terisi dari $booking)
    }
});
</script>
@endsection