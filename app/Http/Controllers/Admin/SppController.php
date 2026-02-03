<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Spp;
use Illuminate\Http\Request;

class SppController extends Controller
{
  
    public function index()
    {
        $spp = Spp::orderBy('tahun', 'desc')->paginate(15);
        return view('admin.spp.index', compact('spp'));
    }

    
    public function create()
    {
        return view('admin.spp.create');
    }

   
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

   
    public function edit(Spp $spp)
    {
        return view('admin.spp.edit', compact('spp'));
    }

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

    
    public function destroy(Spp $spp)
    {
       
        if ($spp->pembayaran()->count() > 0) {
            return redirect()->route('admin.spp.index')
                ->with('error', 'Data SPP tidak dapat dihapus karena sudah digunakan dalam pembayaran.');
        }

        $spp->delete();

        return redirect()->route('admin.spp.index')
            ->with('success', 'Data SPP berhasil dihapus.');
    }
}

