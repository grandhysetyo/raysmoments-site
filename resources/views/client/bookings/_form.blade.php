{{-- 
  PARTIAL FORM UNTUK KLIEN
  
  Variabel yang Dibutuhkan:
  - $packages (selalu)
  - $addons (selalu)
  
  Variabel Opsional (untuk mode edit/perubahan):
  - $booking (objek booking)
  - $currentAddonIds (array ID addon yang sudah dipilih)
--}}

{{-- Keamanan CSRF, akan di-include di dalam tag <form> induk --}}
  @csrf

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      {{-- KOLOM KIRI: DATA DETAIL & ACARA (2/3 LEBAR LAYAR) --}}
      <div class="lg:col-span-2 space-y-8">
          
          {{-- SECTION 1: DATA KLIEN --}}
          {{-- Bagian ini hanya muncul di mode "Create" (saat $booking tidak ada) --}}
          @if (!isset($booking))
          <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
              <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">1. Detail Kontak & Akun Baru</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="md:col-span-2">
                      <label for="email" class="block mb-1 font-medium text-gray-700">Email (Penting untuk konfirmasi)</label>
                      <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded p-2 @error('email') border-red-500 @enderror" required>
                      @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
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
                  </div>
                  <div>
                      <label for="university" class="block mb-1 font-medium text-gray-700">Universitas/Institusi (Opsional)</label>
                      <input type="text" name="university" id="university" value="{{ old('university') }}" class="w-full border border-gray-300 rounded p-2">
                  </div>
                  <div>
                      <label for="faculty_or_major" class="block mb-1 font-medium text-gray-700">Fakultas/Jurusan (Opsional)</label>
                      <input type="text" name="faculty_or_major" id="faculty_or_major" value="{{ old('faculty_or_major') }}" class="w-full border border-gray-300 rounded p-2">
                  </div>
              </div>
          </div>
          @endif
          
          {{-- SECTION 2: DETAIL ACARA (BISA DIUBAH) --}}
          <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
              <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">
                  {{-- Judul dinamis --}}
                  {{ isset($booking) ? '2. Detail Acara & Lokasi (Perubahan)' : '2. Detail Acara & Lokasi' }}
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  
                  <div>
                      <label for="event_date" class="block mb-1 font-medium text-gray-700">Tanggal Acara</label>
                      {{-- Logika "Smart Value": old() ATAU $booking->event_date ATAU '' --}}
                      <input type="date" name="event_date" id="event_date" 
                             value="{{ old('event_date', $booking->event_date ?? '') }}" 
                             class="w-full border border-gray-300 rounded p-2 @error('event_date') border-red-500 @enderror" required>
                      @error('event_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
  
                  <div>
                      <label for="session_1_time" class="block mb-1 font-medium text-gray-700">Waktu Sesi 1</label>
                      <input type="time" name="session_1_time" id="session_1_time" 
                             value="{{ old('session_1_time', $booking->session_1_time ?? '') }}" 
                             class="w-full border border-gray-300 rounded p-2 @error('session_1_time') border-red-500 @enderror" required>
                      @error('session_1_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
  
                  <div id="session_2_client_container" style="display:none;">
                      <label for="session_2_time" class="block mb-1 font-medium text-gray-700">Waktu Sesi 2</label>
                      <input type="time" name="session_2_time" id="session_2_time" 
                             value="{{ old('session_2_time', $booking->session_2_time ?? '') }}" 
                             class="w-full border border-gray-300 rounded p-2 @error('session_2_time') border-red-500 @enderror">
                      @error('session_2_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
                  
                  <div>
                      <label for="event_city" class="block mb-1 font-medium text-gray-700">Kota Acara</label>
                      <input type="text" name="event_city" id="event_city" 
                             value="{{ old('event_city', $booking->event_city ?? '') }}" 
                             class="w-full border border-gray-300 rounded p-2 @error('event_city') border-red-500 @enderror" required>
                      @error('event_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
                  
                  <div class="md:col-span-2">
                      <label for="event_location" class="block mb-1 font-medium text-gray-700">Lokasi Detail (Nama Tempat)</label>
                      <input type="text" name="event_location" id="event_location" 
                             value="{{ old('event_location', $booking->event_location ?? '') }}" 
                             class="w-full border border-gray-300 rounded p-2 @error('event_location') border-red-500 @enderror" required>
                      @error('event_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
                  
                  <div class="md:col-span-3">
                      <label for="notes" class="block mb-1 font-medium text-gray-700">
                          {{ isset($booking) ? 'Catatan/Alasan Perubahan' : 'Catatan (Opsional)' }}
                      </label>
                      <textarea name="notes" id="notes" class="w-full border border-gray-300 rounded p-2">{{ old('notes', $booking->notes ?? '') }}</textarea>
                      @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                  </div>
  
              </div>
          </div> {{-- END SECTION 2 --}}
          
      </div> {{-- END KOLOM KIRI --}}
  
  
      {{-- KOLOM KANAN: PAKET & RINGKASAN HARGA --}}
      <div class="lg:col-span-1 space-y-8 lg:sticky lg:top-8">
          
          {{-- SECTION 3: PAKET & ADD-ONS --}}
          <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
              <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">
                  {{ isset($booking) ? '3. Paket & Add-ons (Perubahan)' : '3. Paket & Add-ons' }}
              </h2>
              
              <div class="mb-5">
                  <label class="block mb-1 font-medium text-gray-700">Pilih Paket</label>
                  <select name="package_id" id="package_id" class="w-full border border-gray-300 rounded p-2 @error('package_id') border-red-500 @enderror" required>
                      {{-- Tampilkan placeholder HANYA jika mode create --}}
                      @if (!isset($booking))
                      <option value="" data-price="0" data-duration="0">-- Pilih Paket --</option>
                      @endif
                      
                      @foreach($packages as $package)
                          <option value="{{ $package->id }}" 
                                  data-price="{{ $package->price }}" 
                                  data-duration="{{ $package->duration_hours ?? 1 }}" {{-- Sesuai kode Anda --}}
                                  {{-- Logika "Smart Selected" --}}
                                  @selected(old('package_id', $booking->package_id ?? '') == $package->id)>
                              {{ $package->name }} (Rp {{ number_format($package->price, 0, ',', '.') }})
                          </option>
                      @endforeach
                  </select>
                  @error('package_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
  
              <div class="mb-5 border-t border-gray-200 pt-4">
                  <label class="block mb-2 font-medium text-gray-700">Pilih Add-ons (Opsional)</label>
                  <div class="space-y-2 text-sm max-h-40 overflow-y-auto p-2 border rounded bg-gray-50">
                      @foreach($addons as $addon)
                          <div>
                              <label class="inline-flex items-center">
                                  {{-- Logika "Smart Checked" --}}
                                  <input type="checkbox" name="addons[]" value="{{ $addon->id }}" 
                                         class="form-checkbox text-indigo-600 addon-checkbox rounded-sm" 
                                         data-price="{{ $addon->price }}" 
                                         @checked(in_array($addon->id, old('addons', $currentAddonIds ?? [])))>
                                  <span class="ml-2 text-gray-700">{{ $addon->name }} (+Rp {{ number_format($addon->price,0) }})</span>
                              </label>
                          </div>
                      @endforeach
                  </div>
              </div>
          </div>
    
          {{-- SECTION 4: RINGKASAN HARGA --}}
          <div class="bg-white p-6 rounded-xl shadow-xl border border-gray-200">
              <h2 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">
                  {{ isset($booking) ? '4. Ringkasan Harga (Baru)' : '4. Ringkasan Pembayaran' }}
              </h2>
              <div class="mb-5">
                <label class="block text-lg font-semibold text-gray-800">Pilihan Pembayaran Awal</label>
                <p class="text-sm text-gray-500 mb-3">Pilih metode pembayaran awal Anda.</p>
                <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-2 sm:space-y-0">
                    
                    {{-- Opsi 1: Bayar DP --}}
                    <label for="pay_dp" class="flex-1 flex items-center p-3 border rounded-lg cursor-pointer bg-gray-50 hover:border-indigo-500">
                        <input type="radio" name="payment_option" id="pay_dp" value="dp" class="text-indigo-600 focus:ring-indigo-500" checked>
                        <span class="ml-3">
                            <span class="block font-medium text-gray-700">Bayar DP (50%)</span>
                        </span>
                    </label>
                    
                    {{-- Opsi 2: Bayar Lunas --}}
                    <label for="pay_full" class="flex-1 flex items-center p-3 border rounded-lg cursor-pointer bg-gray-50 hover:border-indigo-500">
                        <input type="radio" name="payment_option" id="pay_full" value="full" class="text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-3">
                            <span class="block font-medium text-gray-700">Bayar Lunas (100%)</span>
                        </span>
                    </label>
                </div>
                @error('payment_option')
                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-3 border-t pt-4">
                {{-- Harga Paket (Sudah Ada) --}}
                <div class="flex justify-between text-base text-gray-600">
                    <span>Harga Paket:</span>
                    <span id="package_price" class="font-semibold">Rp 0</span>
                    {{-- (Input hidden Anda biarkan) --}}
                    <input type="hidden" name="package_price_hidden" id="package_price_hidden">
                </div>
                
                {{-- Total Add-ons (Sudah Ada) --}}
                <div class="flex justify-between text-base text-gray-600 border-b pb-2">
                    <span>Total Add-ons:</span>
                    <span id="addons_total" class="font-semibold">Rp 0</span>
                    {{-- (Input hidden Anda biarkan) --}}
                    <input type="hidden" name="addons_total_hidden" id="addons_total_hidden">
                </div>
                
                {{-- Grand Total (Sudah Ada) --}}
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-2">
                    <span>Grand Total:</span>
                    <span id="grand_total_display" class="text-red-600">Rp 0</span>
                    {{-- (Input hidden Anda biarkan) --}}
                    <input type="hidden" name="grand_total" id="grand_total"> 
                </div>

                {{-- ========================================================== --}}
                {{-- BARIS BARU YANG KITA TAMBAHKAN --}}
                {{-- ========================================================== --}}
                <div class="border-t pt-3 space-y-1">
                    <div class="flex justify-between text-md font-semibold text-blue-600">
                        <span id="dp-label-text">Bayar DP (50%):</span>
                        <span id="dp-amount-text">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-md font-semibold text-gray-600">
                        <span id="final-label-text">Sisa Tagihan (50%):</span>
                        <span id="final-amount-text">Rp 0</span>
                    </div>
                </div>
                {{-- ========================================================== --}}

            </div>
  
              {{-- Tombol Submit Dinamis Diletakkan di Sini --}}
              <button type="submit" class="w-full mt-6 px-4 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition duration-150 shadow-md">
                  {{ isset($booking) ? 'Ajukan Perubahan' : 'Buat & Lanjutkan ke Pembayaran' }}
              </button>
              
              {{-- Tombol Batal hanya untuk mode edit --}}
              @isset($booking)
              <a href="{{ route('tracking.show', $booking->order_code) }}" class="block text-center w-full mt-2 text-sm text-gray-600 hover:text-gray-800">
                  Batal
              </a>
              @endisset
  
          </div> {{-- END SECTION 4 --}}
      </div> {{-- END KOLOM KANAN --}}
  </div>