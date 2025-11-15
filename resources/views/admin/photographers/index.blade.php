@extends('layouts.admin')

@section('title', 'Manajemen Fotografer')
@section('page-title', 'Manajemen Fotografer')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Fotografer</h2>
        <a href="{{ route('admin.photographers.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
           + Tambah Fotografer
        </a>
    </div>

    {{-- (Pesan Sukses/Error di sini) --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesialisasi</th>
                    
                    {{-- KOLOM BARU --}}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarif</th>
                    
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                @forelse($photographers as $photographer)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $photographer->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $photographer->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $photographer->photographerProfile?->speciality ?? 'N/A' }}</td>
                        
                        {{-- SEL BARU UNTUK TARIF --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{-- Kita loop data rates yang sudah di-load --}}
                            @forelse($photographer->photographerProfile?->rates ?? [] as $rate)
                                <div class="text-xs mb-1">
                                    <span class="font-medium">{{ $rate->city }}:</span>
                                    <span>Rp {{ number_format($rate->base_rate, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <span class="text-xs text-gray-400">Belum ada tarif</span>
                            @endforelse
                        </td>
                        
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($photographer->status == 'active')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('admin.photographers.edit', $photographer) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            
                            <form action="{{ route('admin.photographers.destroy', $photographer) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus fotografer ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada fotografer yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Link Paginasi --}}
    <div class="mt-6">
        {{ $photographers->links() }}
    </div>
</div>
@endsection