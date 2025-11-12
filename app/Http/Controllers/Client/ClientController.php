<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::where('role', 'client')->get();
        return view('clients.index', compact('clients'));
    }

    public function show(User $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(User $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$client->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        $client->update($data);
        return redirect()->route('clients.index')->with('success', 'Client updated');
    }

    public function destroy(User $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Client deleted');
    }
}
