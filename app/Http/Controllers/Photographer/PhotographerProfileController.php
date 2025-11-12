<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\PhotographerProfile;
use Illuminate\Http\Request;

class PhotographerProfileController extends Controller
{
    public function edit(PhotographerProfile $profile)
    {
        return view('photographers.profile', compact('profile'));
    }

    public function update(Request $request, PhotographerProfile $profile)
    {
        $data = $request->validate([
            'default_rate' => 'required|numeric',
            'experience_years' => 'required|integer',
            'speciality' => 'nullable|string',
            'bio' => 'nullable|string',
            'portfolio_url' => 'nullable|url',
        ]);

        $profile->update($data);
        return redirect()->back()->with('success', 'Profile updated');
    }
}
