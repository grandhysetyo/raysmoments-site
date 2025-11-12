<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ClientDetail;
use Illuminate\Http\Request;

class ClientDetailController extends Controller
{
    public function edit(ClientDetail $clientDetail)
    {
        return view('clients.edit', compact('clientDetail'));
    }

    public function update(Request $request, ClientDetail $clientDetail)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'faculty_or_major' => 'nullable|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'instagram' => 'nullable|string|max:255',
        ]);

        $clientDetail->update($data);

        return redirect()->back()->with('success', 'Client detail updated');
    }
}
