@extends('layouts.admin')

@section('title', 'Tambah Paket Baru')
@section('page-title', 'Tambah Paket Baru')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.packages.store') }}" method="POST">
            {{-- 
                @include akan memuat file _form.blade.php di sini.
                Kita tidak mengirim variabel $package, jadi form akan kosong.
            --}}
            @include('admin.packages._form')
        </form>

    </div>
@endsection