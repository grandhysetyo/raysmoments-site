<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PhotographerController extends Controller
{
    public function index()
    {
        $photographers = User::where('role', 'photographer')->get();
        return view('photographers.index', compact('photographers'));
    }

    public function show(User $photographer)
    {
        return view('photographers.show', compact('photographer'));
    }

    public function edit(User $photographer)
    {
        return view('photographers.edit', compact('photographer'));
    }

    public function update(Request $request, User $photographer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$photographer->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        $photographer->update($data);
        return redirect()->route('photographers.index')->with('success', 'Photographer updated');
    }

    public function destroy(User $photographer)
    {
        $photographer->delete();
        return redirect()->route('photographers.index')->with('success', 'Photographer deleted');
    }
}
