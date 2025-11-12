@extends('layouts.admin')

@section('title', 'Edit Addons')
@section('page-title', 'Edit Addons')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.addons.update', $addon) }}" method="POST">
            @method('PUT') {{-- <-- Penting untuk method update --}}
            
            {{-- 
                @include akan memuat file _form.blade.php di sini.
                Kita mengirimkan ['addon' => $addon], 
                sehingga form akan terisi data yang ada.
            --}}
            @include('admin.addons._form', ['addon' => $addon])
        </form>

    </div>
@endsection