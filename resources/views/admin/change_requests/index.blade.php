@extends('layouts.admin')

@section('title', 'Change Requests')
@section('page-title', 'Daftar Permintaan Perubahan')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Permintaan Perubahan (Pending)</h2>
            {{-- Opsional: Filter atau tombol lain --}}
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Request</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Perubahan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Est. Biaya</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $req)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{-- <a href="{{ route('admin.bookings.show', $req->booking->id) }}" class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                    {{ $req->booking->order_code }}
                                </a> --}}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $req->booking->user->clientDetails->full_name ?? $req->booking->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $req->booking->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $req->created_at->format('d M Y, H:i') }}
                                <br><span class="text-xs text-gray-400">{{ $req->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex flex-wrap gap-1">
                                    @if($req->booking->event_date != $req->new_event_date)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Jadwal</span>
                                    @endif
                                    @if($req->new_package_id && $req->new_package_id != $req->booking->package_id)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Paket</span>
                                    @endif
                                    @if($req->booking->event_location != $req->new_event_location)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Lokasi</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                @if($req->additional_cost > 0)
                                    <span class="text-red-600">+ Rp {{ number_format($req->additional_cost, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-green-600">Update DP / Rp 0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium whitespace-nowrap">
                                <a href="{{ route('admin.change-requests.show', $req->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium px-3 py-1 border border-indigo-600 rounded-md transition duration-150">
                                    Tinjau & Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-check-circle text-4xl mb-3 text-green-200"></i>
                                    <p>Tidak ada permintaan perubahan pending.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    </div>
@endsection