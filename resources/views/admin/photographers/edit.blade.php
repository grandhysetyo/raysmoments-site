@extends('layouts.admin')

@section('title', 'Edit Fotografer')
@section('page-title', 'Edit Fotografer: ' . $photographer->name)

@section('content')

    {{-- Pesan Sukses (jika ada setelah update) --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('rate_success'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('rate_success') }}</span>
        </div>
    @endif

    {{-- BAGIAN 1: FORM EDIT PROFIL --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.photographers.update', $photographer) }}" method="POST">
            @method('PUT')
            
            {{-- Menggunakan partial form yang sama dengan create --}}
            @include('admin.photographers._form', ['photographer' => $photographer])
        </form>

    </div>

    {{-- BAGIAN 2: MANAJEMEN TARIF (BARU) --}}
    <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Manajemen Tarif</h2>

        {{-- Form untuk MENAMBAH tarif baru --}}
        {{-- CATATAN: 'photographer-rates.store' adalah rute yang perlu Anda buat selanjutnya --}}
        <form action="{{ route('admin.photographer-rates.store', $photographer->photographerProfile->id) }}" method="POST" class="mb-6">
            @csrf
            <input type="hidden" name="photographer_profile_id" value="{{ $photographer->photographerProfile->id }}">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tambah Tarif Baru</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700">Kota</label>
                    <input type="text" name="city" id="city" 
                           class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div>
                    <label for="base_rate" class="block text-sm font-medium text-gray-700">Tarif Dasar (Rp)</label>
                    <input type="number" name="base_rate" id="base_rate" 
                           class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" required min="0">
                </div>
                <div>
                    <label for="transport_fee" class="block text-sm font-medium text-gray-700">Biaya Transport (Rp)</label>
                    <input type="number" name="transport_fee" id="transport_fee" 
                           class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" min="0" value="0">
                </div>
                <div class="self-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition duration-200 w-full">
                        + Tambah Tarif
                    </button>
                </div>
            </div>
        </form>

        <hr class="my-6">

        <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Tarif Aktif</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarif Dasar</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Biaya Transport</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($photographer->photographerProfile->rates ?? [] as $rate)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $rate->city }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">Rp {{ number_format($rate->base_rate, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">Rp {{ number_format($rate->transport_fee, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                            <form action="{{ route('admin.photographer-rates.destroy', $rate->id) }}" method="POST" onsubmit="return confirm('Hapus tarif ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center text-gray-500">Belum ada tarif yang ditambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection