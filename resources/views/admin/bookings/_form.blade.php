{{-- 
    Partial View ini dipanggil dari create.blade.php dan edit.blade.php.
    Variabel yang harus tersedia: $packages, $addons.
    Variabel opsional (untuk mode edit): $booking, $clientDetails. 
--}}

@php
    // Inisialisasi variabel untuk mode create (jika tidak dikirimkan)
    $booking = $booking ?? new App\Models\Booking();
    $user = $booking->user ?? new App\Models\User();
    $clientDetails = $clientDetails ?? ($user->clientDetails ?? new App\Models\ClientDetail());

    // Ambil addons yang sudah dipilih untuk mode edit
    $selectedAddonIds = $booking->bookingAddons->pluck('addon_id')->toArray() ?? old('addons', []);

    // Ambil nilai Grand Total yang akan ditampilkan (untuk edit, ambil dari DB)
    $grandTotalValue = old('grand_total', $booking->grand_total ?? '');
    // Tentukan opsi pembayaran default (hanya untuk mode edit)
    $defaultPaymentOption = 'dp'; // Default untuk 'create'
    if ($booking->exists) {
        // Cek pembayaran 'Final'
        $finalPaymentAmount = $booking->payments()
                                  ->where('payment_type', 'Final')
                                  ->value('amount');
        
        // Jika pembayaran final adalah 0, berarti dia bayar 'full'
        if ($finalPaymentAmount !== null && (float)$finalPaymentAmount == 0) {
            $defaultPaymentOption = 'full';
        }
    }
    // Ambil dari old() dulu, jika tidak ada, pakai default yg sudah kita hitung
    $currentPaymentOption = old('payment_option', $defaultPaymentOption);
    
    // Ambil nilai DP. JS akan memperbaruinya saat load.
    $dpAmountValue = old('dp_amount', $booking->grand_total ? ($booking->grand_total * 0.5) : '');
@endphp


<h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">1. Data Akun Klien (Wajib)</h3>
<p class="text-sm text-gray-500 mb-4">Password default: 12345678</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label for="full_name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $clientDetails->full_name) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email Klien</label>
        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700">No. WhatsApp</label>
        <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $clientDetails->whatsapp_number) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('whatsapp_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="instagram" class="block text-sm font-medium text-gray-700">Instagram Klien (Opsional)</label>
        <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $clientDetails->instagram) }}" class="mt-1 w-full border border-gray-300 rounded-md">
        @error('instagram')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">2. Data Akademik (Opsional)</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label for="university" class="block text-sm font-medium text-gray-700">Universitas/Institusi</label>
        <input type="text" name="university" id="university" value="{{ old('university', $clientDetails->university) }}" class="mt-1 w-full border border-gray-300 rounded-md">
        @error('university')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="faculty_or_major" class="block text-sm font-medium text-gray-700">Fakultas/Jurusan</label>
        <input type="text" name="faculty_or_major" id="faculty_or_major" value="{{ old('faculty_or_major', $clientDetails->faculty_or_major) }}" class="mt-1 w-full border border-gray-300 rounded-md">
        @error('faculty_or_major')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>

<h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">3. Detail Pesanan</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label for="package_id" class="block text-sm font-medium text-gray-700">Pilih Paket</label>
        <select name="package_id" id="package_id" class="mt-1 w-full border border-gray-300 rounded-md" required>
            <option value="" data-price="0" data-duration="0">-- Pilih Paket --</option>
            @foreach($packages as $package)
                <option value="{{ $package->id }}" 
                        data-price="{{ $package->price }}" 
                        data-duration="{{ $package->duration_hours ?? 1 }}" {{-- BARU: Asumsi ada atribut 'duration' --}}
                        {{ old('package_id', $booking->package_id) == $package->id ? 'selected' : '' }}>
                    {{ $package->name }} (Rp {{ number_format($package->price) }})
                </option>
            @endforeach
        </select>
        @error('package_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="event_date" class="block text-sm font-medium text-gray-700">Tanggal Acara</label>
        <input type="date" name="event_date" id="event_date" value="{{ old('event_date', $booking->event_date?->format('Y-m-d')) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('event_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="session_1_time" class="block text-sm font-medium text-gray-700">Waktu Sesi 1</label>
        <input type="time" name="session_1_time" id="session_1_time" value="{{ old('session_1_time', $booking->session_1_time?->format('H:i')) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('session_1_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    
    <div id="session_2_container" style="display:none;">
        <label for="session_2_time" class="block text-sm font-medium text-gray-700">Waktu Sesi 2</label>
        <input type="time" name="session_2_time" id="session_2_time" value="{{ old('session_2_time', $booking->session_2_time?->format('H:i')) }}" class="mt-1 w-full border border-gray-300 rounded-md">
        @error('session_2_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    
    <div>
        <label for="event_location" class="block text-sm font-medium text-gray-700">Lokasi Acara (Nama Tempat)</label>
        <input type="text" name="event_location" id="event_location" value="{{ old('event_location', $booking->event_location) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('event_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    
    <div>
        <label for="event_city" class="block text-sm font-medium text-gray-700">Kota Acara</label>
        <input type="text" name="event_city" id="event_city" value="{{ old('event_city', $booking->event_city) }}" class="mt-1 w-full border border-gray-300 rounded-md" required>
        @error('event_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
      <label for="notes" class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
      <textarea name="notes" id="notes" class="mt-1 w-full border border-gray-300 rounded-md">{{ old('notes', $booking->notes) }}</textarea>
      @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
  </div>
</div>

<h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">4. Add-ons & Kalkulasi Harga</h3>
<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Add-ons (Opsional)</label>
    <div id="addons-container" class="space-y-2 border p-3 rounded-md bg-gray-50">
        @forelse($addons as $addon)
            <label class="flex items-center text-sm">
                <input type="checkbox" name="addons[]" value="{{ $addon->id }}" data-price="{{ $addon->price }}" class="addon-checkbox text-blue-600 focus:ring-blue-500 rounded" 
                    @checked(in_array($addon->id, $selectedAddonIds) || in_array($addon->id, old('addons', [])))>
                <span class="ml-2">{{ $addon->name }} (Rp {{ number_format($addon->price) }})</span>
            </label>
        @empty
            <p class="text-sm text-gray-500">Tidak ada Add-ons aktif saat ini.</p>
        @endforelse
    </div>
    @error('addons')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>

<h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">5. Opsi Pembayaran</h3>
<div class="mb-6">
    <div class="space-y-2 border p-3 rounded-md bg-gray-50">
        <label class="flex items-center text-sm">
            <input type="radio" name="payment_option" value="dp" class="payment-option-radio text-blue-600 focus:ring-blue-500"
                @checked($currentPaymentOption == 'dp')>
            <span class="ml-2">Bayar DP 50% Saja</span>
        </label>
        <label class="flex items-center text-sm">
            <input type="radio" name="payment_option" value="full" class="payment-option-radio text-blue-600 focus:ring-blue-500"
                @checked($currentPaymentOption == 'full')>
            <span class="ml-2">Bayar Lunas (Full Payment)</span>
        </label>
    </div>
    @error('payment_option')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>


<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label for="grand_total" class="block text-sm font-medium text-gray-700">Grand Total (Rp)</label>
        <input type="number" step="0.01" name="grand_total" id="grand_total" value="{{ $grandTotalValue }}" class="mt-1 w-full border border-gray-300 rounded-md bg-gray-100 font-bold" required min="0" readonly>
        @error('grand_total')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="dp_amount" id="dp_amount_label" class="block text-sm font-medium text-gray-700">Jumlah DP Diharapkan (50%)</label>
        <input type="number" step="0.01" name="dp_amount" id="dp_amount" value="{{ $dpAmountValue }}" class="mt-1 w-full border border-gray-300 rounded-md bg-gray-100 font-bold" required min="0" readonly>
        @error('dp_amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
</div>