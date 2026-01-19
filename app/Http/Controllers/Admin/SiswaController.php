<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    /**
     * Menampilkan daftar siswa
     */
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Search berdasarkan nama atau NISN
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && $request->kelas) {
            $query->where('id_kelas', $request->kelas);
        }

        $siswa = $query->orderBy('created_at', 'desc')->paginate(10);
        $kelas = Kelas::all();

        return view('admin.siswa.index', compact('siswa', 'kelas'));
    }

    /**
     * Menampilkan form tambah siswa
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.siswa.create', compact('kelas'));
    }

    /**
     * Menyimpan data siswa baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'nisn' => 'required|string|unique:siswa,nisn|max:10',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        Siswa::create($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit siswa
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    /**
     * Update data siswa
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id',
            'nisn' => 'required|string|max:10|unique:siswa,nisn,' . $siswa->id,
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $siswa->update($validated);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diupdate.');
    }

    /**
     * Hapus data siswa
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}

