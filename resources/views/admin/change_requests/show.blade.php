@extends('layouts.admin')

@section('title', 'Tinjau Permintaan')
@section('page-title', 'DETAIL REQUEST: ' . $request->booking->order_code)

@section('content')
{{-- Alert Error --}}
@if ($errors->any())
    <div class="max-w-4xl mx-auto p-4 mb-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <strong class="font-bold">Oops! Ada kesalahan:</strong>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-2xl border-t-4 border-indigo-600">

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-6 border-b pb-4">
        <div>
            <h1 class="text-3xl font-extrabold text-indigo-700">TINJAU PERUBAHAN</h1>
            <p class="text-sm text-gray-500">Diajukan pada: {{ $request->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="text-right">
            <a href="{{ route('admin.change-requests.index') }}" class="text-sm text-gray-500 hover:text-indigo-600 underline">
                &larr; Kembali ke Daftar
            </a>
            <div class="mt-2">
                <span class="text-lg font-bold px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">
                    {{ $request->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- A. ALASAN PERUBAHAN --}}
    <div class="mb-8 p-5 border border-indigo-100 bg-indigo-50 rounded-lg">
        <h3 class="text-lg font-semibold mb-2 text-indigo-800">Alasan Klien:</h3>
        <p class="text-gray-700 italic">"{{ $request->reason }}"</p>
    </div>

    {{-- B. PERBANDINGAN DATA (TABEL) --}}
    <h3 class="text-xl font-semibold mb-3 border-b pb-1 text-gray-800">Perbandingan Data</h3>
    <div class="overflow-x-auto mb-8 border rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/3">Atribut</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3 bg-red-50">Data Lama (Saat Ini)</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-1/3 bg-green-50">Data Baru (Diminta)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                
                {{-- Tanggal --}}
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Tanggal Acara</td>
                    <td class="px-6 py-4 text-gray-600 bg-red-50/30">
                        {{ \Carbon\Carbon::parse($request->old_event_date)->isoFormat('dddd, D MMMM YYYY') }}
                    </td>
                    <td class="px-6 py-4 font-semibold bg-green-50/30 {{ $request->old_event_date != $request->new_event_date ? 'text-green-700' : 'text-gray-500' }}">
                        {{ \Carbon\Carbon::parse($request->new_event_date)->isoFormat('dddd, D MMMM YYYY') }}
                        @if($request->old_event_date != $request->new_event_date) <i class="fas fa-exchange-alt ml-1 text-xs"></i> @endif
                    </td>
                </tr>

                {{-- Lokasi --}}
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Lokasi & Kota</td>
                    <td class="px-6 py-4 text-gray-600 bg-red-50/30">
                        {{ $request->old_event_location }} ({{ $request->old_event_city }})
                    </td>
                    <td class="px-6 py-4 font-semibold bg-green-50/30 {{ $request->old_event_location != $request->new_event_location ? 'text-green-700' : 'text-gray-500' }}">
                        {{ $request->new_event_location }} ({{ $request->new_event_city }})
                        @if($request->old_event_location != $request->new_event_location) <i class="fas fa-exchange-alt ml-1 text-xs"></i> @endif
                    </td>
                </tr>

                {{-- Paket (Jika Berubah) --}}
                @if($request->new_package_id)
                <tr>
                    <td class="px-6 py-4 font-medium text-gray-900">Paket Layanan</td>
                    <td class="px-6 py-4 text-gray-600 bg-red-50/30">
                        {{ $request->booking->package->name }}
                    </td>
                    <td class="px-6 py-4 font-semibold bg-green-50/30 {{ $request->new_package_id != $request->booking->package_id ? 'text-green-700' : 'text-gray-500' }}">
                        {{ $request->newPackage->name }}
                        @if($request->new_package_id != $request->booking->package_id) <i class="fas fa-exchange-alt ml-1 text-xs"></i> @endif
                    </td>
                </tr>
                @endif

            </tbody>
        </table>
    </div>

    {{-- C. ANALISIS BIAYA --}}
    <h3 class="text-xl font-semibold mb-3 border-b pb-1 text-gray-800">Analisis Keuangan</h3>
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm mb-8">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Status Pembayaran Saat Ini</p>
                <p class="text-lg font-bold text-gray-800 mt-1">
                    Total Terverifikasi: Rp {{ number_format($request->booking->payments()->where('status', 'Verified')->sum('amount'), 0, ',', '.') }}
                </p>
            </div>
            <div class="text-right border-l pl-6">
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Biaya Tambahan (Selisih)</p>
                @if($request->additional_cost > 0)
                    <p class="text-3xl font-extrabold text-red-600 mt-1">+ Rp {{ number_format($request->additional_cost, 0, ',', '.') }}</p>
                    <p class="text-xs text-red-500 mt-1">*Tagihan baru akan dibuat otomatis.</p>
                @else
                    <p class="text-3xl font-extrabold text-green-600 mt-1">Rp 0</p>
                    <p class="text-xs text-gray-500 mt-1">*Nominal DP lama akan diupdate (jika belum bayar).</p>
                @endif
            </div>
        </div>
    </div>

    <hr class="my-8">

    {{-- D. AKSI ADMIN --}}
    <h3 class="text-xl font-semibold mb-4 text-gray-800">Konfirmasi Tindakan</h3>
    
    <div class="grid grid-cols-1 gap-6">
        {{-- FORM APPROVE --}}
        <form action="{{ route('admin.change-requests.approve', $request->id) }}" method="POST" class="bg-green-50 p-6 rounded-lg border border-green-200">
            @csrf
            <h4 class="font-bold text-lg mb-2 text-green-800">1. Setujui Perubahan</h4>
            <p class="text-sm text-gray-600 mb-4">Data booking akan langsung diperbarui dan tagihan disesuaikan.</p>
            
            <div class="mb-4">
                <label for="admin_notes_approve" class="block text-sm font-medium text-gray-700 mb-1">Catatan Persetujuan (Opsional)</label>
                <input type="text" name="admin_notes" id="admin_notes_approve" class="w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring focus:ring-green-200" placeholder="Contoh: Tanggal tersedia, disetujui." value="Permintaan disetujui.">
            </div>

            <button type="submit" onclick="return confirm('Yakin setujui perubahan ini?')" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                <i class="fas fa-check-circle mr-2"></i> SETUJUI PERMINTAAN
            </button>
        </form>

        {{-- FORM REJECT --}}
        <div class="bg-red-50 p-6 rounded-lg border border-red-200">
            <h4 class="font-bold text-lg mb-2 text-red-800">2. Tolak Permintaan</h4>
            <p class="text-sm text-gray-600 mb-4">Jika tanggal penuh atau permintaan tidak wajar.</p>
            
            {{-- Trigger Modal --}}
            <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-200">
                <i class="fas fa-times-circle mr-2"></i> TOLAK PERMINTAAN
            </button>
        </div>
    </div>

</div>

{{-- Modal Reject (Hidden by default) --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('rejectModal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.change-requests.reject', $request->id) }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Tolak Permintaan</h3>
                    <p class="text-sm text-gray-500 mb-4">Berikan alasan penolakan kepada klien.</p>
                    <textarea name="admin_notes" rows="3" class="w-full border border-gray-300 rounded-md p-2 focus:ring-red-500 focus:border-red-500" required placeholder="Contoh: Maaf, tanggal tersebut sudah penuh."></textarea>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Tolak Sekarang</button>
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection