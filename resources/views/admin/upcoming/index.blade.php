@extends('layouts.admin')

@section('title', 'Upcoming Shooting')
@section('page-title', 'Daftar Job (DP Verified)')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Acara</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fotografer (Fee)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                        {{ $booking->order_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $booking->user->clientDetails->full_name ?? $booking->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($booking->event_date)->isoFormat('D MMM YYYY') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        @if($booking->photographer)
                            <span class="font-semibold text-green-700">{{ $booking->photographer->name }}</span>
                            <br>
                            
                            {{-- LOGIKA BARU UNTUK TOTAL FEE --}}
                            @php
                                $total_fee = $booking->photographer_rate + $booking->photographer_other_costs;
                            @endphp
                            <span class="text-xs text-gray-500">
                                Total Fee: Rp {{ number_format($total_fee, 0) }}
                            </span>
                            
                            {{-- Tampilkan breakdown jika ada biaya lain --}}
                            @if($booking->photographer_other_costs > 0)
                                <span class="text-xs text-gray-400 italic block">(Rate: {{ number_format($booking->photographer_rate, 0) }} + Lain: {{ number_format($booking->photographer_other_costs, 0) }})</span>
                            @endif

                        @else
                            <span class="text-red-500 italic">Belum di-assign</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($booking->status == 'Photographer Assigned')
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Ditugaskan</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Siap Tugaskan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.upcoming.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $booking->photographer_id ? 'Edit' : 'Assign' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada job yang menunggu assignment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection