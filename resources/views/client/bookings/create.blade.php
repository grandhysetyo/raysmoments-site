@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4 md:p-8 mt-6">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-900 border-b pb-3">📝 Buat Pemesanan Fotografi</h1>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: DATA DETAIL & ACARA (2/3 LEBAR LAYAR) --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- SECTION 1: DATA KLIEN & AKUN BARU --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">1. Detail Kontak & Akun Baru</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Email (Account Creation) --}}
                        <div class="md:col-span-2">
                            <label for="email" class="block mb-1 font-medium text-gray-700">Email (Penting untuk konfirmasi)</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded p-2 @error('email') border-red-500 @enderror" required>
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Akun akan dibuat otomatis. Anda akan melacak pesanan menggunakan kode unik.</p>
                        </div>
                        
                        {{-- Field Kontak --}}
                        <div>
                            <label for="full_name" class="block mb-1 font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" class="w-full border border-gray-300 rounded p-2 @error('full_name') border-red-500 @enderror" required>
                            @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="whatsapp_number" class="block mb-1 font-medium text-gray-700">No. WhatsApp</label>
                            <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number') }}" class="w-full border border-gray-300 rounded p-2 @error('whatsapp_number') border-red-500 @enderror" required>
                            @error('whatsapp_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="instagram" class="block mb-1 font-medium text-gray-700">Instagram (Opsional)</label>
                            <input type="text" name="instagram" id="instagram" value="{{ old('instagram') }}" class="w-full border border-gray-300 rounded p-2 @error('instagram') border-red-500 @enderror">
                            @error('instagram')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        {{-- Akademik (Optional) --}}
                        <div>
                            <label for="university" class="block mb-1 font-medium text-gray-700">Universitas/Institusi (Opsional)</label>
                            <input type="text" name="university" id="university" value="{{ old('university') }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                        <div>
                            <label for="faculty_or_major" class="block mb-1 font-medium text-gray-700">Fakultas/Jurusan (Opsional)</label>
                            <input type="text" name="faculty_or_major" id="faculty_or_major" value="{{ old('faculty_or_major') }}" class="w-full border border-gray-300 rounded p-2">
                        </div>
                    </div>
                </div> {{-- END SECTION 1 --}}

                
                {{-- SECTION 2: DETAIL ACARA --}}
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">2. Detail Acara & Lokasi</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div>
                            <label for="event_date" class="block mb-1 font-medium text-gray-700">Tanggal Acara</label>
                            <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}" class="w-full border border-gray-300 rounded p-2 @error('event_date') border-red-500 @enderror" required>
                            @error('event_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div>
                            <label for="event_city" class="block mb-1 font-medium text-gray-700">Kota Acara</label>
                            <input type="text" name="event_city" id="event_city" value="{{ old('event_city') }}" class="w-full border border-gray-300 rounded p-2 @error('event_city') border-red-500 @enderror" required>
                            @error('event_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="event_location" class="block mb-1 font-medium text-gray-700">Lokasi Detail (Nama Tempat)</label>
                            <input type="text" name="event_location" id="event_location" value="{{ old('event_location') }}" class="w-full border border-gray-300 rounded p-2 @error('event_location') border-red-500 @enderror" required>
                            @error('event_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                    </div>
                </div> {{-- END SECTION 2 --}}
                
            </div> {{-- END KOLOM KIRI --}}


            {{-- KOLOM KANAN: PAKET & RINGKASAN HARGA --}}
            <div class="lg:col-span-1 space-y-8 lg:sticky lg:top-8">
                
                {{-- SECTION 3: PAKET & ADD-ONS --}}
                <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">3. Paket & Add-ons</h2>
                    
                    {{-- Paket Selection --}}
                    <div class="mb-5">
                        <label class="block mb-1 font-medium text-gray-700">Pilih Paket</label>
                        <select name="package_id" id="package" class="w-full border border-gray-300 rounded p-2 @error('package_id') border-red-500 @enderror" required>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->price }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} (Rp {{ number_format($package->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Add-ons Checkboxes --}}
                    <div class="mb-5 border-t border-gray-200 pt-4">
                        <label class="block mb-2 font-medium text-gray-700">Pilih Add-ons (Opsional)</label>
                        <div class="space-y-2 text-sm max-h-40 overflow-y-auto p-2 border rounded bg-gray-50">
                            @foreach($addons as $addon)
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="addons[]" value="{{ $addon->id }}" class="form-checkbox text-indigo-600 addon-checkbox rounded-sm" data-price="{{ $addon->price }}" @checked(in_array($addon->id, old('addons', [])))>
                                        <span class="ml-2 text-gray-700">{{ $addon->name }} (+Rp {{ number_format($addon->price,0) }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: RINGKASAN HARGA --}}
                <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
                    <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">4. Ringkasan Pembayaran</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-base text-gray-600 border-b pb-2">
                            <span>Harga Paket:</span>
                            <span id="package_price" class="font-semibold">Rp 0</span>
                            <input type="hidden" name="package_price_hidden" id="package_price_hidden">
                        </div>
                        <div class="flex justify-between text-base text-gray-600 border-b pb-2">
                            <span>Total Add-ons:</span>
                            <span id="addons_total" class="font-semibold">Rp 0</span>
                            <input type="hidden" name="addons_total_hidden" id="addons_total_hidden">
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-900 border-t pt-3">
                            <span>Grand Total:</span>
                            <span id="grand_total_display" class="text-red-600">Rp 0</span>
                            <input type="hidden" name="grand_total" id="grand_total_hidden"> 
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 px-4 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                        Buat & Lanjutkan ke Pembayaran
                    </button>
                </div> {{-- END SECTION 4 --}}
            </div> {{-- END KOLOM KANAN --}}
        </div>
    </form>
</div>

<script>
    const packageSelect = document.getElementById('package');
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
    
    // Display Elements
    const packagePriceDisplay = document.getElementById('package_price');
    const addonsTotalDisplay = document.getElementById('addons_total');
    const grandTotalDisplay = document.getElementById('grand_total_display');
    
    // Hidden Input Elements for Controller Submission
    const grandTotalHidden = document.getElementById('grand_total_hidden');
    const packagePriceHidden = document.getElementById('package_price_hidden');
    const addonsTotalHidden = document.getElementById('addons_total_hidden');


    function formatRupiah(number) {
        return 'Rp ' + (Math.round(number)).toLocaleString('id-ID');
    }

    function calculateTotal() {
        const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
        const packagePrice = parseFloat(selectedPackage.dataset.price) || 0;

        let addonsTotal = 0;
        addonCheckboxes.forEach(cb => {
            if (cb.checked) {
                addonsTotal += parseFloat(cb.dataset.price) || 0;
            }
        });

        const grandTotal = packagePrice + addonsTotal;

        // Update Display
        packagePriceDisplay.textContent = formatRupiah(packagePrice);
        addonsTotalDisplay.textContent = formatRupiah(addonsTotal);
        grandTotalDisplay.textContent = formatRupiah(grandTotal);

        // Update Hidden Inputs
        grandTotalHidden.value = grandTotal.toFixed(2);
        packagePriceHidden.value = packagePrice.toFixed(2);
        addonsTotalHidden.value = addonsTotal.toFixed(2);
    }

    // Attach event listeners
    packageSelect.addEventListener('change', calculateTotal);
    addonCheckboxes.forEach(cb => cb.addEventListener('change', calculateTotal));

    // Initial calculation on load
    calculateTotal();
</script>
@endsection