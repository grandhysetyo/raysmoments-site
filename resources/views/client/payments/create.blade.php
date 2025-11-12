@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-6 p-6 bg-white shadow rounded">
    <h1 class="text-xl font-bold mb-4">Upload Bukti Pembayaran</h1>
    <form action="{{ route('client.payments.store', $booking->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-medium">Tipe Pembayaran</label>
            <select name="payment_type" class="w-full border rounded p-2">
                <option value="DP">DP</option>
                <option value="Final">Final</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-medium">File Bukti</label>
            <input type="file" name="file" class="w-full border rounded p-2">
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Upload</button>
    </form>
</div>
@endsection
