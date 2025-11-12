{{-- 
    File ini akan di-include oleh create.blade.php dan edit.blade.php
    Variabel $package akan ada jika ini mode 'edit', dan tidak ada jika mode 'create'
--}}

@csrf

<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
    <input type="text" name="name" id="name" 
           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                  focus:outline-none focus:ring-blue-500 focus:border-blue-500
                  @error('name') border-red-500 @enderror"
           value="{{ old('name', $package->name ?? '') }}" required>
    @error('name')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
    <textarea name="description" id="description" rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                     focus:outline-none focus:ring-blue-500 focus:border-blue-500
                     @error('description') border-red-500 @enderror"
    >{{ old('description', $package->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div>
        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
        <input type="number" name="price" id="price" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('price') border-red-500 @enderror"
               value="{{ old('price', $package->price ?? '') }}" required min="0">
        @error('price')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durasi (Jam)</label>
        <input type="number" name="duration_hours" id="duration_hours" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('duration_hours') border-red-500 @enderror"
               value="{{ old('duration_hours', $package->duration_hours ?? '') }}" required min="1">
        @error('duration_hours')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="max_photos" class="block text-sm font-medium text-gray-700 mb-1">Max Foto</label>
        <input type="number" name="max_photos" id="max_photos" 
               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                      focus:outline-none focus:ring-blue-500 focus:border-blue-500
                      @error('max_photos') border-red-500 @enderror"
               value="{{ old('max_photos', $package->max_photos ?? '') }}" required min="1">
        @error('max_photos')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label class="flex items-center">
        <input type="checkbox" name="is_active" value="1"
               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
               {{-- Directive @checked akan otomatis menambahkan 'checked' jika kondisinya true --}}
               @checked(old('is_active', $package->is_active ?? false)) 
        >
        <span class="ml-2 text-sm text-gray-700">Aktifkan Paket</span>
    </label>
</div>

<div class="flex justify-end">
    <a href="{{ route('admin.packages.index') }}" 
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200 mr-2">
       Batal
    </a>
    <button type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
        Simpan Paket
    </button>
</div>