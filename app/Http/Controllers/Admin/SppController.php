<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use Illuminate\Http\Request;

class SppController extends Controller
{
    /**
     * Menampilkan daftar SPP
     */
    public function index()
    {
        $spp = Spp::orderBy('tahun', 'desc')->paginate(15);
        return view('admin.spp.index', compact('spp'));
    }

    /**
     * Menampilkan form tambah SPP
     */
    public function create()
    {
        return view('admin.spp.create');
    }

    /**
     * Menyimpan data SPP baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099|unique:spp,tahun',
            'nominal' => 'required|numeric|min:0',
        ], [
            'tahun.unique' => 'Tahun SPP sudah ada. Silakan pilih tahun lain.',
            'tahun.min' => 'Tahun harus minimal 2000.',
            'tahun.max' => 'Tahun harus maksimal 2099.',
            'nominal.min' => 'Nominal harus lebih dari 0.',
        ]);

        Spp::create($validated);

        return redirect()->route('admin.spp.index')
            ->with('success', 'Data SPP berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit SPP
     */
    public function edit(Spp $spp)
    {
        return view('admin.spp.edit', compact('spp'));
    }

    /**
     * Update data SPP
     */
    public function update(Request $request, Spp $spp)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099|unique:spp,tahun,' . $spp->id,
            'nominal' => 'required|numeric|min:0',
        ], [
            'tahun.unique' => 'Tahun SPP sudah ada. Silakan pilih tahun lain.',
            'tahun.min' => 'Tahun harus minimal 2000.',
            'tahun.max' => 'Tahun harus maksimal 2099.',
            'nominal.min' => 'Nominal harus lebih dari 0.',
        ]);

        $spp->update($validated);

        return redirect()->route('admin.spp.index')
            ->with('success', 'Data SPP berhasil diupdate.');
    }

    /**
     * Hapus data SPP
     */
    public function destroy(Spp $spp)
    {
        // Cek apakah SPP sudah digunakan di pembayaran
        if ($spp->pembayaran()->count() > 0) {
            return redirect()->route('admin.spp.index')
                ->with('error', 'Data SPP tidak dapat dihapus karena sudah digunakan dalam pembayaran.');
        }

        $spp->delete();

        return redirect()->route('admin.spp.index')
            ->with('success', 'Data SPP berhasil dihapus.');
    }
}

