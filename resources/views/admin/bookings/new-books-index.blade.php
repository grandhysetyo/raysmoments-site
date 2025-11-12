@extends('layouts.admin')

@section('title', 'New Books')
@section('page-title', 'Pemesanan Baru (Awaiting DP)')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
      <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-800">Daftar Pemesanan (Awaiting DP)</h2>
          <a href="{{ route('admin.bookings.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
               + Buat Pemesanan Baru
            </a>
      </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Universitas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kota</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pesan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $booking->order_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->user->clientDetails->full_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->user->clientDetails->university ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->event_city ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->package->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">Rp {{ number_format($booking->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $booking->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-2">
                                    
                                    {{-- Tombol Lihat & Verifikasi (Workflow) --}}
                                    <a href="{{ route('admin.new-books.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900 font-medium px-2 py-1 border border-indigo-600 rounded-md">
                                        Lihat & Verifikasi
                                    </a>
                                    
                                    {{-- Tombol Edit (CRUD) --}}
                                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="text-yellow-600 hover:text-yellow-900 px-2 py-1 border border-yellow-600 rounded-md">
                                        Edit
                                    </a>
                            
                                    {{-- Tombol Delete (CRUD) --}}
                                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('ANDA YAKIN INGIN MENGHAPUS PEMESANAN {{ $booking->order_code }}? Ini akan menghapus semua pembayaran terkait.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 px-2 py-1 border border-red-600 rounded-md">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada pemesanan yang menunggu DP saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $bookings->links() }}</div>
    </div>
@endsection