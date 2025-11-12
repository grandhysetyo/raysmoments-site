@extends('layouts.admin')

@section('title', 'Tambah Fotografer Baru')
@section('page-title', 'Tambah Fotografer Baru')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        
        <form action="{{ route('admin.photographers.store') }}" method="POST">
            {{-- Form ini hanya berisi data user & profile --}}
            @include('admin.photographers._form')
        </form>

    </div>
@endsection