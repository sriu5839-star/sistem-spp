<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
       
        $totalPembayaran = Pembayaran::where('id_petugas', $user->id)
            ->sum('jumlah_bayar');
        
        $totalTransaksi = Pembayaran::where('id_petugas', $user->id)
            ->count();
        
        
        $pembayaranHariIni = Pembayaran::where('id_petugas', $user->id)
            ->whereDate('tgl_bayar', Carbon::today())
            ->count();
        
        $totalHariIni = Pembayaran::where('id_petugas', $user->id)
            ->whereDate('tgl_bayar', Carbon::today())
            ->sum('jumlah_bayar');
        
      
        $grafikPemasukan = Pembayaran::select(
                DB::raw('MONTH(tgl_bayar) as bulan'),
                DB::raw('YEAR(tgl_bayar) as tahun'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->where('id_petugas', $user->id)
            ->where('tgl_bayar', '>=', now()->subMonths(6))
            ->groupBy('bulan', 'tahun')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();
        
        // Notifikasi transaksi terbaru (5 transaksi terakhir) - hanya yang dibuat oleh petugas ini
        $transaksiTerbaru = Pembayaran::with(['siswa.kelas', 'spp'])
            ->where('id_petugas', $user->id)
            ->orderBy('tgl_bayar', 'desc')
            ->limit(5)
            ->get();
        
        return view('petugas.dashboard', compact(
            'totalPembayaran',
            'totalTransaksi',
            'pembayaranHariIni',
            'totalHariIni',
            'grafikPemasukan',
            'transaksiTerbaru'
        ));
    }
}
