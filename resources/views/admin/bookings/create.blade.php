@extends('layouts.admin')

@section('title', 'Buat Pemesanan Baru')
@section('page-title', 'Buat Pemesanan Baru (Manual)')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md max-w-4xl mx-auto">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('admin.bookings.store') }}" method="POST">
            @csrf

            {{-- MEMANGGIL PARTIAL FORM UTAMA --}}
            @include('admin.bookings._form', ['packages' => $packages, 'addons' => $addons])

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                    Buat Akun Klien & Pesanan
                </button>
            </div>
        </form>
    </div>
    
    @include('admin.bookings._form_js') {{-- Memanggil JS kalkulasi --}}
@endsection