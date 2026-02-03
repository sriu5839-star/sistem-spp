<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\Spp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    
    public function index(Request $request)
    {
        $kelasList = Kelas::orderBy('nama_kelas', 'asc')->get();
        $kelas = $kelasList;
        if ($request->filled('kelas')) {
            $kelas = $kelasList->where('id', (int) $request->kelas);
        }
        $sppAktif = Spp::orderBy('tahun', 'desc')->first();
        
        $rekap = [];
        
        foreach ($kelas as $k) {
            $totalSiswa = $k->siswa->count();
            
           
            $siswaLunas = 0;
            $siswaBelumLunas = 0;
            $totalPembayaran = 0;
            $totalTunggakan = 0;
            
            foreach ($k->siswa as $siswa) {
                
                $totalBayarSiswa = Pembayaran::where('id_siswa', $siswa->id)
                    ->where('tahun_dibayar', $sppAktif->tahun ?? date('Y'))
                    ->where('id_spp', $sppAktif->id ?? null)
                    ->sum('jumlah_bayar');
                
                $totalPembayaran += $totalBayarSiswa;
                
                
                $nominalSpp = $sppAktif->nominal ?? 0;
                if ($nominalSpp > 0 && $totalBayarSiswa >= $nominalSpp) {
                    $siswaLunas++;
                } else {
                    $siswaBelumLunas++;
                    if ($nominalSpp > 0) {
                        $sisaTagihan = $nominalSpp - $totalBayarSiswa;
                        $totalTunggakan += max(0, $sisaTagihan);
                    }
                }
            }
            
            $rekap[] = [
                'kelas' => $k,
                'total_siswa' => $totalSiswa,
                'siswa_lunas' => $siswaLunas,
                'siswa_belum_lunas' => $siswaBelumLunas,
                'total_pembayaran' => $totalPembayaran,
                'total_tunggakan' => $totalTunggakan,
            ];
        }
        
        return view('admin.rekap.index', [
            'rekap' => $rekap,
            'sppAktif' => $sppAktif,
            'kelas' => $kelasList,
        ]);
    }
}
