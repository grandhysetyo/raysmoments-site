{{-- 
    File ini TIDAK memiliki input password.
    Password diatur otomatis HANYA saat pembuatan (di controller).
--}}
@csrf

<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
    <input type="text" name="name" id="name" 
           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                  focus:outline-none focus:ring-blue-500 focus:border-blue-500
                  @error('name') border-red-500 @enderror"
           value="{{ old('name', $user->name ?? '') }}" required>
    @error('name')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
    <input type="email" name="email" id="email" 
           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                  focus:outline-none focus:ring-blue-500 focus:border-blue-500
                  @error('email') border-red-500 @enderror"
           value="{{ old('email', $user->email ?? '') }}" required>
    @error('email')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
        <select name="role" id="role" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring-blue-500 focus:border-blue-500
                       @error('role') border-red-500 @enderror"
                required>
            <option value="" disabled {{ old('role', $user->role ?? '') == '' ? 'selected' : '' }}>Pilih Peran</option>
            <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="owner" {{ old('role', $user->role ?? '') == 'owner' ? 'selected' : '' }}>Owner</option>
        </select>
        @error('role')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" id="status" 
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                       focus:outline-none focus:ring-blue-500 focus:border-blue-500
                       @error('status') border-red-500 @enderror"
                required>
            <option value="active" {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ old('status', $user->status ?? '') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
        @error('status')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex justify-end">
    <a href="{{ route('admin.users.index') }}" 
       class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition duration-200 mr-2">
       Batal
    </a>
    <button type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
        Simpan User
    </button>
</div>