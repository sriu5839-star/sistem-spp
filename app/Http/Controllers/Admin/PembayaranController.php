<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function create(Request $request)
    {
        $kelas = Kelas::all(); 
        $spp = Spp::orderBy('tahun', 'desc')->get();
        $riwayat = Siswa::with(['kelas', 'pembayaran'])->paginate(10);
        $siswa = null; 

        return view('admin.pembayaran.create', compact('siswa', 'spp', 'kelas', 'riwayat'));
    }

    /** * TAMBAHKAN FUNCTION INI AGAR TOMBOL BAYAR BERFUNGSI
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'bulan_dibayar' => 'required|string',
            'tahun_dibayar' => 'required|integer',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        // Cari data SPP untuk menentukan status (Lunas/Belum Lunas)
        // Disini kita ambil SPP terbaru atau sesuaikan dengan tahun_dibayar
        $spp = Spp::where('tahun', $request->tahun_dibayar)->first() ?? Spp::latest()->first();

        // Logika penentuan status
        $status = ($request->jumlah_bayar >= $spp->nominal) ? 'Lunas' : 'Belum Lunas';

        // Simpan data ke database
        Pembayaran::create([
            'id_petugas' => Auth::id(),
            'id_siswa'   => $request->id_siswa,
            'tgl_bayar'  => now(),
            'bulan_dibayar' => $request->bulan_dibayar,
            'tahun_dibayar' => $request->tahun_dibayar,
            'id_spp'     => $spp->id,
            'jumlah_bayar' => $request->jumlah_bayar,
            'status'     => $status
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil disimpan!');
    }
}