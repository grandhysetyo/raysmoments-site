{{-- DETEKSI OTOMATIS MODE --}}
@php
    $isEditMode = isset($booking);
@endphp

@csrf

{{-- === CARD 1: INFORMASI DATA DIRI === --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Informasi Klien
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
            {{-- PERBAIKAN: Gunakan $booking->user->clientDetails --}}
            <input type="text" name="full_name" 
                   value="{{ old('full_name', isset($booking) ? $booking->user->clientDetails->full_name : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500 
                          @error('full_name') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('full_name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
            {{-- Email ada di tabel User, jadi $booking->user->email sudah benar --}}
            <input type="email" name="email" 
                   value="{{ old('email', isset($booking) ? $booking->user->email : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('email') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">No. WhatsApp</label>
            {{-- PERBAIKAN: Gunakan $booking->user->clientDetails --}}
            <input type="text" name="whatsapp_number" 
                   value="{{ old('whatsapp_number', isset($booking) ? $booking->user->clientDetails->whatsapp_number : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('whatsapp_number') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('whatsapp_number')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Instagram (Opsional)</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">@</span>
                {{-- PERBAIKAN: Gunakan $booking->user->clientDetails --}}
                <input type="text" name="instagram" 
                       value="{{ old('instagram', isset($booking) ? $booking->user->clientDetails->instagram : '') }}" 
                       class="w-full pl-8 rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                              @error('instagram') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">
            </div>
            @error('instagram')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Universitas (Opsional)</label>
            {{-- PERBAIKAN: Gunakan $booking->user->clientDetails --}}
            <input type="text" name="university" 
                   value="{{ old('university', isset($booking) ? $booking->user->clientDetails->university : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('university') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">
            @error('university')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Fakultas/Jurusan (Opsional)</label>
            {{-- PERBAIKAN: Gunakan $booking->user->clientDetails --}}
            <input type="text" name="faculty_or_major" 
                   value="{{ old('faculty_or_major', isset($booking) ? $booking->user->clientDetails->faculty_or_major : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('faculty_or_major') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">
            @error('faculty_or_major')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- === CARD 2: DETAIL ACARA === --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        Detail Acara
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Acara</label>
            {{-- Event Date ada langsung di tabel Booking --}}
            <input type="date" name="event_date" 
                   value="{{ old('event_date', isset($booking) ? $booking->event_date->format('Y-m-d') : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('event_date') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('event_date')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Kota</label>
            <input type="text" name="event_city" 
                   value="{{ old('event_city', isset($booking) ? $booking->event_city : '') }}" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('event_city') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('event_city')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi Lengkap (Venue)</label>
            <input type="text" name="event_location" 
                   value="{{ old('event_location', isset($booking) ? $booking->event_location : '') }}" 
                   placeholder="Contoh: Gedung Serbaguna Lt. 2, Jl. Merdeka No. 10"
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('event_location') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('event_location')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
            <textarea name="notes" rows="3" 
                   class="w-full rounded-lg shadow-sm transition focus:ring-indigo-500 focus:border-indigo-500
                          @error('notes') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror"
                   placeholder="Contoh: Request tone warna tertentu, atau info penting lainnya...">{{ old('notes', isset($booking) ? $booking->notes : '') }}</textarea>
            @error('notes')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- === CARD 3: PAKET & LAYANAN === --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Paket & Layanan
    </h3>

    {{-- Pilih Paket --}}
    <div class="mb-6">
        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Paket</label>
        <select name="package_id" id="package_id" 
                class="w-full rounded-lg shadow-sm transition py-3 focus:ring-indigo-500 focus:border-indigo-500
                       @error('package_id') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                required>
            <option value="" data-price="0">-- Pilih Paket --</option>
            @foreach($packages as $pkg)
                <option value="{{ $pkg->id }}" 
                    data-price="{{ $pkg->price }}" 
                    data-duration="{{ $pkg->duration_hours ?? 1 }}"
                    {{ (old('package_id', isset($booking) ? $booking->package_id : '') == $pkg->id) ? 'selected' : '' }}>
                    {{ $pkg->name }} - Rp {{ number_format($pkg->price, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        @error('package_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Jam Sesi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Jam Sesi 1</label>
            <input type="time" name="session_1_time" 
                   value="{{ old('session_1_time', isset($booking) && $booking->session_1_time ? \Carbon\Carbon::parse($booking->session_1_time)->format('H:i') : '') }}" 
                   class="w-full rounded-lg shadow-sm focus:ring-indigo-500 transition
                          @error('session_1_time') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror" 
                   required>
            @error('session_1_time')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        {{-- Container Sesi 2 --}}
        <div id="session_2_client_container" style="display: none;">
            <label class="block text-sm font-bold text-gray-700 mb-2">Jam Sesi 2 (Opsional)</label>
            <input type="time" name="session_2_time" id="session_2_time" 
                   value="{{ old('session_2_time', isset($booking) && $booking->session_2_time ? \Carbon\Carbon::parse($booking->session_2_time)->format('H:i') : '') }}" 
                   class="w-full rounded-lg shadow-sm focus:ring-indigo-500 transition
                          @error('session_2_time') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 @enderror">
            @error('session_2_time')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Add-ons --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-3">Tambahan (Add-ons)</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($addons as $addon)
            <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-indigo-50 transition cursor-pointer">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="addons[]" value="{{ $addon->id }}" 
                        class="addon-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                        data-price="{{ $addon->price }}"
                        {{ (is_array(old('addons', $currentAddonIds ?? [])) && in_array($addon->id, old('addons', $currentAddonIds ?? []))) ? 'checked' : '' }}>
                </div>
                <div class="ml-3 text-sm">
                    <label for="addons" class="font-semibold text-gray-800">{{ $addon->name }}</label>
                    <p class="text-gray-500 text-xs mt-0.5">Rp {{ number_format($addon->price, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @error('addons')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- === CARD 4: KHUSUS MODE EDIT (ALASAN) === --}}
@if($isEditMode)
    <div class="bg-white p-6 rounded-xl shadow-sm border border-red-100 mb-6 ring-1 ring-red-50">
        <h3 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            Konfirmasi Perubahan
        </h3>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Alasan Perubahan <span class="text-red-500">*</span></label>
            <textarea name="reason" rows="3" 
                      class="w-full rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500
                             @error('reason') border-red-500 @else border-gray-300 @enderror" 
                      required placeholder="Contoh: Ingin upgrade paket ke Platinum karena acara diperpanjang..."></textarea>
            @error('reason')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <input type="hidden" id="previous_total_paid" value="{{ $totalPaidVerified ?? 0 }}">
    </div>
@endif


{{-- === CARD 5: RINGKASAN HARGA & PEMBAYARAN === --}}
<div class="bg-gray-900 text-white p-6 rounded-xl shadow-lg mt-8">
    <h3 class="text-xl font-bold mb-6 border-b border-gray-700 pb-4 flex items-center text-white">
        <svg class="w-6 h-6 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ $isEditMode ? 'Ringkasan Perubahan Biaya' : 'Ringkasan & Pembayaran' }}
    </h3>

    <div class="space-y-4 text-sm md:text-base">
        {{-- Rincian Item --}}
        <div class="flex justify-between items-center">
            <span class="text-gray-300">Harga Paket {{ $isEditMode ? '(Baru)' : '' }}</span>
            <span class="font-semibold text-white" id="package_price">Rp 0</span>
            <input type="hidden" name="package_price_hidden" id="package_price_hidden">
        </div>
        <div class="flex justify-between items-center">
            <span class="text-gray-300">Total Add-ons {{ $isEditMode ? '(Baru)' : '' }}</span>
            <span class="font-semibold text-white" id="addons_total">Rp 0</span>
            <input type="hidden" name="addons_total_hidden" id="addons_total_hidden">
        </div>
        
        <div class="border-t border-gray-700 my-2"></div>

        {{-- Grand Total --}}
        <div class="flex justify-between text-lg font-bold text-white">
            <span>Grand Total {{ $isEditMode ? '(Baru)' : '' }}</span>
            <span id="grand_total_display" class="text-yellow-400">Rp 0</span>
            <input type="hidden" name="grand_total" id="grand_total">
        </div>
        
        {{-- Tampilkan error jika ada manipulasi JS vs Backend --}}
        @error('grand_total')
            <div class="bg-red-500 text-white text-xs p-2 rounded mt-2">
                {{ $message }}
            </div>
        @enderror

        {{-- LOGIKA PEMBAYARAN --}}
        <div class="mt-6 bg-gray-800 p-5 rounded-lg border border-gray-700">
            
            @if($isEditMode)
                {{-- === TAMPILAN MODE EDIT / UPGRADE === --}}
                <div class="flex justify-between text-gray-300 mb-2">
                    <span>Total Uang Masuk (Verified)</span>
                    <span class="font-bold text-green-400"> - Rp {{ number_format($totalPaidVerified ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center mt-4 pt-4 border-t border-dashed border-gray-600">
                    <span class="font-bold text-white w-2/3">
                        Kekurangan DP <br>
                        <span class="text-xs text-gray-400 font-normal">Wajib dibayar (Target: 50% dari Grand Total Baru)</span>
                    </span>
                    <span class="text-2xl font-extrabold text-red-400" id="summary_additional_cost">Rp 0</span>
                </div>

            @else
                {{-- === TAMPILAN MODE BOOKING BARU === --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-300 mb-3">Opsi Pembayaran</label>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <label class="flex items-center cursor-pointer p-3 rounded border border-gray-600 hover:bg-gray-700 transition w-full">
                            <input type="radio" name="payment_option" value="dp" checked class="text-indigo-500 focus:ring-indigo-500 bg-gray-700 border-gray-500">
                            <span class="ml-2 text-white font-medium">Bayar DP 50%</span>
                        </label>
                        <label class="flex items-center cursor-pointer p-3 rounded border border-gray-600 hover:bg-gray-700 transition w-full">
                            <input type="radio" name="payment_option" value="full" class="text-indigo-500 focus:ring-indigo-500 bg-gray-700 border-gray-500">
                            <span class="ml-2 text-white font-medium">Bayar Lunas (100%)</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-300" id="dp-label-text">Bayar DP (50%):</span>
                    <span class="font-bold text-white text-xl" id="dp-amount-text">Rp 0</span>
                </div>
                <div class="flex justify-between items-center text-sm text-gray-400 mt-1">
                    <span id="final-label-text">Sisa Tagihan (50%):</span>
                    <span id="final-amount-text">Rp 0</span>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- === TOMBOL SUBMIT === --}}
<div class="mt-8 flex justify-end">
    @if($isEditMode)
        <a href="{{ route('tracking.show', $booking->order_code) }}" class="mr-4 px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
            Batal
        </a>
        <button type="submit" class="px-8 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg transition transform hover:-translate-y-0.5 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Ajukan Perubahan
        </button>
    @else
        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-indigo-600 text-white text-lg font-bold rounded-xl hover:bg-indigo-700 shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center">
            Buat Pemesanan Sekarang 
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </button>
    @endif
</div>