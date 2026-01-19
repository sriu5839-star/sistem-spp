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
        // Total pembayaran masuk
        $totalPembayaran = Pembayaran::sum('jumlah_bayar');
        
        // Jumlah siswa
        $totalSiswa = Siswa::count();
        
        // Ambil SPP aktif (tahun terbaru)
        $sppAktif = \App\Models\Spp::orderBy('tahun', 'desc')->first();
        
        // Hitung siswa lunas dan belum lunas berdasarkan total pembayaran vs nominal SPP
        // Siswa dianggap lunas jika total pembayaran untuk tahun SPP aktif >= nominal SPP
        $siswaLunas = 0;
        $siswaBelumLunas = 0;
        
        if ($sppAktif) {
            $allSiswa = Siswa::all();
            
            foreach ($allSiswa as $siswa) {
                // Hitung total pembayaran siswa untuk tahun SPP aktif
                $totalBayarSiswa = Pembayaran::where('id_siswa', $siswa->id)
                    ->where('tahun_dibayar', $sppAktif->tahun)
                    ->where('id_spp', $sppAktif->id)
                    ->sum('jumlah_bayar');
                
                // Tentukan status berdasarkan pembayaran vs nominal SPP
                // Siswa dianggap lunas jika sudah bayar minimal 1x nominal SPP untuk tahun tersebut
                if ($sppAktif->nominal > 0 && $totalBayarSiswa >= $sppAktif->nominal) {
                    $siswaLunas++;
                } else {
                    $siswaBelumLunas++;
                }
            }
        } else {
            // Jika tidak ada SPP, hitung berdasarkan status pembayaran terakhir
            $siswaLunas = Siswa::whereHas('pembayaran', function($query) {
                $query->where('status', 'Lunas');
            })->count();
            $siswaBelumLunas = $totalSiswa - $siswaLunas;
        }
        
        // Grafik pemasukan per bulan (6 bulan terakhir)
        // Ambil 6 bulan terakhir dari bulan terakhir yang ada data pembayaran
        $grafikPemasukan = collect();
        
        // Cari bulan terakhir yang ada data pembayaran
        $pembayaranTerakhir = Pembayaran::orderBy('tgl_bayar', 'desc')->first();
        
        if ($pembayaranTerakhir) {
            // Gunakan bulan terakhir yang ada data sebagai referensi
            $bulanTerakhir = \Carbon\Carbon::parse($pembayaranTerakhir->tgl_bayar);
            
            // Ambil 6 bulan terakhir dari bulan terakhir yang ada data
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
            // Jika tidak ada data, tampilkan 6 bulan terakhir dari sekarang
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $grafikPemasukan->push((object)[
                    'bulan' => (int) $date->month,
                    'tahun' => (int) $date->year,
                    'total' => 0.0
                ]);
            }
        }
        
        // Notifikasi transaksi terbaru (5 transaksi terakhir)
        $transaksiTerbaru = Pembayaran::with(['siswa.kelas', 'petugas'])
            ->orderBy('tgl_bayar', 'desc')
            ->limit(5)
            ->get();
        
        // Statistik tambahan
        $totalPembayaranBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->sum('jumlah_bayar');
        
        $totalPembayaranHariIni = Pembayaran::whereDate('tgl_bayar', now()->toDateString())
            ->sum('jumlah_bayar');
        
        $jumlahTransaksiBulanIni = Pembayaran::whereMonth('tgl_bayar', now()->month)
            ->whereYear('tgl_bayar', now()->year)
            ->count();
        
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
            'sppAktif'
        ));
    }
}
