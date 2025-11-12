<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Menampilkan daftar paket (Tabel).
     */
    public function index()
    {
        $packages = Package::latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    /**
     * Menampilkan form untuk membuat paket baru (Form).
     */
    public function create()
    {
        return view('admin.packages.create');
    }

    /**
     * Menyimpan paket baru ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'max_photos' => 'required|integer|min:1',
        ]);

        // Tambahkan 'is_active' secara manual
        // request->boolean() akan mengembalikan true jika nilainya "on", 1, "true", atau true
        // dan false untuk selain itu (termasuk jika tidak ada/unchecked)
        $validatedData['is_active'] = $request->boolean('is_active');

        Package::create($validatedData);

        return redirect()->route('admin.packages.index')
                         ->with('success', 'Paket baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit paket (Form).
     */
    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    /**
     * Memperbarui paket di database.
     */
    public function update(Request $request, Package $package)
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

        return redirect()->route('admin.packages.index')
                         ->with('success', 'Paket berhasil diperbarui.');
    }

    /**
     * Menghapus paket dari database.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('admin.packages.index')
                         ->with('success', 'Paket berhasil dihapus.');
    }
}