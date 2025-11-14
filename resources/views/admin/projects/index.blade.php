@extends('layouts.admin')

@section('title', 'List Project')
@section('page-title', 'Daftar Proyek Aktif')

@section('content')
<div class="bg-white shadow-lg rounded-lg p-6">
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Klien</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Acara</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Booking</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Final Payment</th>
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
                        <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">{{ $booking->status }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{-- Logika untuk status payment --}}
                        @if($booking->status === 'Pending Final Payment')
                            <span class="px-2 py-1 text-xs font-semibold text-blue-800 bg-blue-100 rounded-full">Pending Verifikasi</span>
                        @elseif($booking->status === 'Fully Paid')
                            <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Sudah Lunas</span>
                        @elseif($booking->status === 'Awaiting Final Payment')
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">Menunggu Upload</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 rounded-full">Belum Ditagih</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        
                        {{-- JIKA STATUS = PENDING FINAL PAYMENT, TAMPILKAN TOMBOL VERIFIKASI --}}
                        @if($booking->status === 'Pending Final Payment')
                            <a href="{{ route('admin.projects.show', $booking) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Verifikasi
                            </a>
                        
                        {{-- JIKA STATUS LAIN, TAMPILKAN FORM UPDATE STATUS --}}
                        @else
                            <form action="{{ route('admin.projects.update_status', $booking) }}" method="POST" class="flex items-center">
                                @csrf
                                <select name="status" class="block w-full text-xs border-gray-300 rounded-md shadow-sm py-1 pl-2 pr-7">
                                    {{-- Tampilkan status saat ini --}}
                                    <option value="" disabled selected>Ubah ke...</option>
                                    
                                    {{-- Opsi yang relevan --}}
                                    @if($booking->status === 'Photographer Assigned')
                                        <option value="Shooting Completed">Shooting Completed</option>
                                    @endif
                                    
                                    @if(in_array($booking->status, ['Shooting Completed', 'Originals Delivered']))
                                        <option value="Originals Delivered">Originals Delivered</option>
                                        <option value="Edits In Progress">Edits In Progress</option>
                                    @endif
                                    
                                    @if(in_array($booking->status, ['Edits In Progress', 'Edits Delivered']))
                                        <option value="Edits Delivered">Edits Delivered</option>
                                    @endif

                                    {{-- Opsi untuk menagih & menutup proyek --}}
                                    <option value="Awaiting Final Payment" class="font-bold text-blue-600">!! TAGIH FINAL PAYMENT !!</option>
                                    <option value="Project Closed" class="font-bold text-red-600">Project Closed</option>
                                </select>
                                <button type="submit" class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                                    OK
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada proyek aktif.
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