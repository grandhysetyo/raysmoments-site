@csrf

<h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Data Akun</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <input type="text" name="name" id="name" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('name') border-red-500 @enderror"
               value="{{ old('name', $photographer->name ?? '') }}" required>
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" id="email" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('email') border-red-500 @enderror"
               value="{{ old('email', $photographer->email ?? '') }}" required>
        @error('email')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>
<p class="text-sm text-gray-500 mt-2">
    @if(!isset($photographer))
    * Password akan diatur otomatis ke: <strong>12345678</strong>
    @else
    * Form ini tidak mengubah password user.
    @endif
</p>

<hr class="my-6">

<h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Data Profil</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="speciality" class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi</label>
        <input type="text" name="speciality" id="speciality" 
               placeholder="Contoh: Wedding, Graduation"
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('speciality') border-red-500 @enderror"
               value="{{ old('speciality', $photographer->profile?->speciality ?? '') }}">
        @error('speciality')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-1">Pengalaman (Tahun)</label>
        <input type="number" name="experience_years" id="experience_years" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('experience_years') border-red-500 @enderror"
               value="{{ old('experience_years', $photographer->profile?->experience_years ?? 0) }}" min="0">
        @error('experience_years')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-6">
    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio Singkat</label>
    <textarea name="bio" id="bio" rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                     focus:outline-none focus:ring-blue-500 focus:border-blue-500
                     @error('bio') border-red-500 @enderror"
    >{{ old('bio', $photographer->profile?->bio ?? '') }}</textarea>
    @error('bio')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>


<div class="flex justify-end mt-8">
    <a href="{{ route('admin.photographers.index') }}" 
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200 mr-2">
       Batal
    </a>
    <button type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
        Simpan Profil
    </button>
</div>