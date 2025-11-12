@extends('layouts.admin')

@section('title', 'Edit Paket')
@section('page-title', 'Edit Paket')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.packages.update', $package) }}" method="POST">
            @method('PUT') {{-- <-- Penting untuk method update --}}
            
            {{-- 
                @include akan memuat file _form.blade.php di sini.
                Kita mengirimkan ['package' => $package], 
                sehingga form akan terisi data yang ada.
            --}}
            @include('admin.packages._form', ['package' => $package])
        </form>

    </div>
@endsection