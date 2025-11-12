@extends('layouts.admin')

@section('title', 'Tambah Addons Baru')
@section('page-title', 'Tambah Addons Baru')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.addons.store') }}" method="POST">
            {{-- 
                @include akan memuat file _form.blade.php di sini.
                Kita tidak mengirim variabel $addon, jadi form akan kosong.
            --}}
            @include('admin.addons._form')
        </form>

    </div>
@endsection