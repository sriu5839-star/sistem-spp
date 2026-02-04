<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
      
        $totalPembayaran = Pembayaran::sum('jumlah_bayar');
        
      
        $totalSiswa = Siswa::count();
        
        
        $sppAktif = \App\Models\Spp::orderBy('tahun', 'desc')->first();
        
        
        $siswaLunas = 0;
        $siswaBelumLunas = 0;
        
        if ($sppAktif) {
            $allSiswa = Siswa::all();
            
            foreach ($allSiswa as $siswa) {
               
                $totalBayarSiswa = Pembayaran::where('id_siswa', $siswa->id)
                    ->where('tahun_dibayar', $sppAktif->tahun)
                    ->where('id_spp', $sppAktif->id)
                    ->sum('jumlah_bayar');
                
                if ($sppAktif->nominal > 0 && $totalBayarSiswa >= $sppAktif->nominal) {
                    $siswaLunas++;
                } else {
                    $siswaBelumLunas++;
                }
            }
        } else {
            
            $siswaLunas = Siswa::whereHas('pembayaran', function($query) {
                $query->where('status', 'Lunas');
            })->count();
            $siswaBelumLunas = $totalSiswa - $siswaLunas;
        }
        
       
        $grafikPemasukan = collect();
        
       
        $pembayaranTerakhir = Pembayaran::orderBy('tgl_bayar', 'desc')->first();
        
        if ($pembayaranTerakhir) {
            
            $bulanTerakhir = \Carbon\Carbon::parse($pembayaranTerakhir->tgl_bayar);
            
            
            for ($i = 5; $i >= 0; $i--) {
                $date = $bulanTerakhir->copy()->subMonths($i);
                $totalBulan = Pembayaran::whereMonth('tgl_bayar', $date->month)
                    ->whereYear('tgl_bayar', $date->year)
                    ->sum('jumlah_bayar');
                
                $grafikPemasukan->push((object)[
                    'bulan' => (int) $date->month,
                    'tahun' => (int) $date->year,
                    'total' => (float) $totalBulan
                ]);
            }
        } else {
           
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $grafikPemasukan->push((object)[
                    'bulan' => (int) $date->month,
                    'tahun' => (int) $date->year,
                    'total' => 0.0
                ]);
            }
        }
        
       
        $transaksiTerbaru = Pembayaran::with(['siswa.kelas', 'petugas'])
            ->orderBy('tgl_bayar', 'desc')
            ->limit(5)
            ->get();
        
       
        $totalPembayaranBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');
        
        $totalPembayaranHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())
            ->sum('jumlah_bayar');
        
        $jumlahTransaksiBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->count();
        
        $sppList = \App\Models\Spp::orderBy('tahun', 'desc')->get();
        $siswaList = Siswa::orderBy('nama', 'asc')->get();

        return view('admin.dashboard', compact(
            'totalPembayaran',
            'totalSiswa',
            'siswaLunas',
            'siswaBelumLunas',
            'grafikPemasukan',
            'transaksiTerbaru',
            'totalPembayaranBulanIni',
            'totalPembayaranHariIni',
            'jumlahTransaksiBulanIni',
            'sppAktif',
            'sppList',
            'siswaList'
        ));
    }
}
