@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4 md:p-8 mt-6">
    <h1 class="text-3xl font-extrabold mb-2 text-gray-900">Ajukan Perubahan Pesanan</h1>
    <p class="text-lg text-gray-600 mb-8 border-b pb-3">
        Untuk pesanan: <strong class="text-indigo-600">{{ $booking->order_code }}</strong>
    </p>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if($hasPaidDP)
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative mb-4">
        <strong class="font-bold">Catatan:</strong> Anda hanya dapat mengubah ke paket dengan harga yang sama atau lebih tinggi.
    </div>
    @endif

    <form action="{{ route('edit.store', $booking->order_code) }}" method="POST">
        
        {{-- Include partial form YANG SAMA.
             Variabel $booking, $packages, $addons, $currentAddonIds 
             dilewatkan oleh Controller dan otomatis terpakai oleh partial. --}}
        @include('client.bookings._form')
        
    </form>
</div>

{{-- Sertakan JS YANG SAMA --}}
@include('client.bookings._form_js')

@endsection