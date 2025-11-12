@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-8 bg-white shadow-xl rounded-xl mt-16 border-t-4 border-indigo-600">
    <h1 class="text-3xl font-extrabold mb-4 text-gray-800">Lacak Pesanan Anda</h1>
    <p class="text-gray-600 mb-6">Masukkan kode pesanan unik (CLI-xxxx) yang Anda terima di email.</p>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('client.tracking.show', ['order_code' => 'temp']) }}" method="GET" id="tracking-form">
        {{-- order_code diisi sementara 'temp', akan diganti oleh JS --}}
        
        <div class="mb-6">
            <label for="order_code_input" class="block mb-2 font-medium text-gray-700">Kode Pesanan (Order Code)</label>
            <input type="text" id="order_code_input" name="order_code_input" class="w-full border border-gray-300 rounded p-3 text-lg font-mono tracking-wider focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: CLI-1762918937" required>
        </div>
        
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md">
            Lacak Status
        </button>
    </form>
</div>

<script>
    document.getElementById('tracking-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('order_code_input').value.trim();
        const form = document.getElementById('tracking-form');
        
        if (input) {
            // Dapatkan URL rute dengan placeholder
            let actionUrl = form.getAttribute('action');
            // Ganti placeholder 'temp' dengan nilai input yang sebenarnya
            actionUrl = actionUrl.replace('temp', input);
            
            // Arahkan browser ke URL tracking yang benar
            window.location.href = actionUrl;
        }
    });
</script>
@endsection