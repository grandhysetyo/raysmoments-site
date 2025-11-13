@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-4 md:p-8 mt-6">
    <h1 class="text-3xl font-extrabold mb-8 text-gray-900 border-b pb-3">📝 Buat Pemesanan Fotografi</h1>
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('bookings.store') }}" method="POST">
        
        {{-- Include partial form. 
             Semua variabel ($packages, $addons) otomatis dilewatkan. --}}
        @include('client.bookings._form')
        
    </form>
</div>

{{-- Sertakan JS --}}
@include('client.bookings._form_js')

@endsection