<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Menampilkan daftar Kelas
     */
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas', 'asc')->paginate(15);
        return view('admin.kelas.index', compact('kelas'));
    }

    /**
     * Menampilkan form tambah Kelas
     */
    public function create()
    {
        return view('admin.kelas.create');
    }

    /**
     * Menyimpan data Kelas baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kompetensi_keahlian' => 'required|string|max:255',
        ], [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
            'kompetensi_keahlian.required' => 'Kompetensi keahlian harus diisi.',
        ]);

        Kelas::create($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit Kelas
     */
    public function edit(Kelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    /**
     * Update data Kelas
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'kompetensi_keahlian' => 'required|string|max:255',
        ], [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
            'kompetensi_keahlian.required' => 'Kompetensi keahlian harus diisi.',
        ]);

        $kelas->update($validated);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diupdate.');
    }

    /**
     * Hapus data Kelas
     */
    public function destroy(Kelas $kelas)
    {
        // Cek apakah kelas sudah digunakan di siswa
        if ($kelas->siswa()->count() > 0) {
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Data kelas tidak dapat dihapus karena sudah digunakan oleh siswa.');
        }

        $kelas->delete();

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}

