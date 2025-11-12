<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerRate;
use Illuminate\Http\Request;

class PhotographerRateController extends Controller
{
    public function index()
    {
        $rates = PhotographerRate::all();
        return view('photographers.rates', compact('rates'));
    }

    public function create()
    {
        return view('photographers.rates_create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'photographer_id' => 'required|exists:photographer_profiles,id',
            'city' => 'required|string',
            'base_rate' => 'required|numeric',
            'transport_fee' => 'nullable|numeric',
            'effective_start' => 'required|date',
            'effective_end' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        PhotographerRate::create($data);
        return redirect()->route('photographer.rates.index')->with('success', 'Rate added');
    }

    public function edit(PhotographerRate $rate)
    {
        return view('photographers.rates_edit', compact('rate'));
    }

    public function update(Request $request, PhotographerRate $rate)
    {
        $data = $request->validate([
            'city' => 'required|string',
            'base_rate' => 'required|numeric',
            'transport_fee' => 'nullable|numeric',
            'effective_start' => 'required|date',
            'effective_end' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $rate->update($data);
        return redirect()->route('photographer.rates.index')->with('success', 'Rate updated');
    }

    public function destroy(PhotographerRate $rate)
    {
        $rate->delete();
        return redirect()->route('photographer.rates.index')->with('success', 'Rate deleted');
    }
}
