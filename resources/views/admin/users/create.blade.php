@extends('layouts.admin')

@section('title', 'Tambah User Baru')
@section('page-title', 'Tambah User Baru')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.users.store') }}" method="POST">
            {{-- 
                @include akan memuat file _form.blade.php.
                Variabel $user tidak didefinisikan, jadi form akan kosong.
            --}}
            @include('admin.users._form')
        </form>

    </div>
@endsection