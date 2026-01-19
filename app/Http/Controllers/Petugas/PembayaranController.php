<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    /**
     * Menampilkan form input pembayaran
     */
    public function create(Request $request)
    {
        $siswa = null;
        $spp = Spp::orderBy('tahun', 'desc')->get();

        // Jika ada parameter siswa_id, load data siswa
        if ($request->has('siswa_id')) {
            $siswa = Siswa::with('kelas')->find($request->siswa_id);
        }

        return view('petugas.pembayaran.create', compact('siswa', 'spp'));
    }

    /**
     * Menyimpan data pembayaran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'id_spp' => 'required|exists:spp,id',
            'bulan_dibayar' => 'required|string',
            'tahun_dibayar' => 'required|integer',
            'tgl_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        // Ambil data SPP untuk mendapatkan nominal
        $spp = Spp::find($validated['id_spp']);
        
        // Tentukan status berdasarkan jumlah bayar vs nominal SPP
        if ($validated['jumlah_bayar'] >= $spp->nominal) {
            $validated['status'] = 'Lunas';
        } else {
            $validated['status'] = 'Belum Lunas';
        }

        // Set petugas yang melakukan pembayaran
        $validated['id_petugas'] = Auth::id();

        Pembayaran::create($validated);

        return redirect()->route('petugas.riwayat.index')
            ->with('success', 'Pembayaran berhasil disimpan.');
    }

    /**
     * Search siswa untuk autocomplete
     */
    public function searchSiswa(Request $request)
    {
        $query = $request->get('q');
        
        $siswa = Siswa::with('kelas')
            ->where('nama', 'like', '%' . $query . '%')
            ->orWhere('nisn', 'like', '%' . $query . '%')
            ->limit(10)
            ->get();

        return response()->json($siswa);
    }
}

