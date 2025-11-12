<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    /**
     * Menampilkan daftar addons (Tabel).
     */
    public function index()
    {
        $addons = Addon::latest()->paginate(10);
        return view('admin.addons.index', compact('addons'));
    }

    /**
     * Menampilkan form untuk membuat addon baru (Form).
     */
    public function create()
    {
        return view('admin.addons.create');
    }

    /**
     * Menyimpan addon baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
        ]);

        // Tambahkan 'is_active' secara manual
        // request->boolean() akan mengembalikan true jika nilainya "on", 1, "true", atau true
        // dan false untuk selain itu (termasuk jika tidak ada/unchecked)
        $validatedData['is_active'] = $request->boolean('is_active');

        Addon::create($validatedData);

        return redirect()->route('admin.addons.index')
                         ->with('success', 'Addon baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit addon (Form).
     */
    public function edit(Addon $addon)
    {
        return view('admin.addons.edit', compact('addon'));
    }

    /**
     * Memperbarui addons di database.
     */
    public function update(Request $request, Addon $addons)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'max_photos' => 'required|integer|min:1',
        ]);
        
        $validatedData['is_active'] = $request->boolean('is_active');

        $package->update($validatedData);

        return redirect()->route('admin.addons.index')
                         ->with('success', 'Addon berhasil diperbarui.');
    }

    /**
     * Menghapus addon dari database.
     */
    public function destroy(Addon $addon)
    {
        $addon->delete();

        return redirect()->route('admin.addons.index')
                         ->with('success', 'Addon berhasil dihapus.');
    }
}