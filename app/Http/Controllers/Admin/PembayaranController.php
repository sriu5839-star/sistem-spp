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
        $siswaList = Siswa::all(); 
        $selectedSiswa = null;
        $tagihan = [];

        if ($request->has('id_siswa')) {
            $selectedSiswa = Siswa::find($request->id_siswa);
            
            if ($selectedSiswa) {
               
                $years = [2024, 2025, 2026];
                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'July', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                
                $sppData = Spp::whereIn('tahun', $years)->get()->keyBy('tahun'); 

                foreach ($years as $year) {
                  
                    $nominal = $sppData[$year]->nominal ?? 0;
                    if ($nominal == 0) {
                       
                        $lastSpp = Spp::latest()->first();
                        $nominal = $lastSpp ? $lastSpp->nominal : 0;
                    }

                    foreach ($months as $index => $month) {
                        
                        $pembayaran = Pembayaran::where('id_siswa', $selectedSiswa->id)
                                                ->where('tahun_dibayar', $year)
                                                ->where('bulan_dibayar', $month)
                                                ->first();

                        $status = $pembayaran ? 'Lunas' : 'Belum Lunas';
                        
                        $tagihan[] = [
                            'tahun' => $year,
                            'bulan' => $month,
                            'nominal' => $nominal,
                            'status' => $status,
                            'is_paid' => (bool)$pembayaran
                        ];
                    }
                }
            }
        }

        return view('admin.pembayaran.create', compact('siswaList', 'selectedSiswa', 'tagihan'));
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'bulan_dibayar' => 'required|string',
            'tahun_dibayar' => 'required|integer',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        
        $spp = Spp::where('tahun', $request->tahun_dibayar)->first() ?? Spp::latest()->first();

       
        $status = ($request->jumlah_bayar >= $spp->nominal) ? 'Lunas' : 'Belum Lunas';

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