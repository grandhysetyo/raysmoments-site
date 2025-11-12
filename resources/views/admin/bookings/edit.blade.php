@extends('layouts.admin')

@section('title', 'Edit Pemesanan: ' . $booking->order_code)
@section('page-title', 'Edit Pemesanan: ' . $booking->order_code)

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md max-w-4xl mx-auto">
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        {{-- FORM EDIT PROFIL UTAMA --}}
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- MEMANGGIL PARTIAL FORM UTAMA, mengirim data Booking --}}
            @include('admin.bookings._form', ['packages' => $packages, 'addons' => $addons, 'booking' => $booking])
            
            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    Perbarui Pesanan
                </button>
            </div>
        </form>
    </div>
    
    {{-- Di sini Anda bisa menambahkan seksi Manajemen Tarif/Rates unik lainnya --}}

    @include('admin.bookings._form_js') {{-- Memanggil JS kalkulasi --}}
@endsection