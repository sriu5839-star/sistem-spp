<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /**
     * Menampilkan rekap pembayaran per kelas (Format Horizontal)
     */
    public function index(Request $request)
    {
        // 1. Ambil data Siswa sebagai base query
        $query = Siswa::with(['kelas', 'pembayaran']);

        // Filter berdasarkan Search (Nama/NISN)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan Kelas
        if ($request->filled('kelas')) {
            $query->where('id_kelas', $request->kelas);
        }

        // 2. Ambil data dengan pagination agar tidak berat
        $riwayat = $query->paginate(15)->appends($request->except('page', 'print'));

        // 3. Data pendukung untuk filter dan header tabel
        $kelas = Kelas::all();
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('admin.riwayat.index', compact('riwayat', 'kelas', 'bulanList'));
    }

    /**
     * Download/cetak nota pembayaran
     */
    public function downloadNota(Pembayaran $pembayaran)
    {
        $pembayaran->load(['siswa.kelas', 'spp', 'petugas']);
        return view('admin.riwayat.nota', compact('pembayaran'));
    }
}
