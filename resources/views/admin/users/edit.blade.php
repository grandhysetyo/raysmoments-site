@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @method('PUT') {{-- <-- Penting untuk method update --}}
            
            {{-- 
                @include akan memuat file _form.blade.php.
                Kita mengirimkan ['user' => $user], 
                sehingga form akan terisi data yang ada.
            --}}
            @include('admin.users._form', ['user' => $user])
        </form>

    </div>
@endsection