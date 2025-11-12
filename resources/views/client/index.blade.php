@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<h1 class="text-2xl font-bold mb-4">Clients</h1>

<table class="w-full bg-white shadow rounded">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left">Phone</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $client)
        <tr class="border-b">
            <td class="px-4 py-2">{{ $client->name }}</td>
            <td class="px-4 py-2">{{ $client->email }}</td>
            <td class="px-4 py-2">{{ $client->phone }}</td>
            <td class="px-4 py-2">{{ ucfirst($client->status) }}</td>
            <td class="px-4 py-2">
                <a href="{{ route('clients.show', $client->id) }}" class="text-blue-600 hover:underline">View</a>
                <a href="{{ route('clients.edit', $client->id) }}" class="ml-2 text-green-600 hover:underline">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
