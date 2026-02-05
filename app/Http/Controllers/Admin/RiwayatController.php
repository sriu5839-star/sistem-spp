<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Spp;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
   
    public function index(Request $request)
    {
        
        $tahunList = Spp::orderBy('tahun', 'asc')->pluck('tahun');
        $tahunDipilih = $request->filled('tahun') ? (int) $request->tahun : ($tahunList->count() ? (int) $tahunList->last() : (int) date('Y'));

        $query = Siswa::with([
            'kelas', 
            'pembayaran' => function($q) use ($tahunDipilih) {
                $q->where('tahun_dibayar', $tahunDipilih);
            }
        ]);

        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

       
        if ($request->filled('kelas')) {
            $query->where('id_kelas', $request->kelas);
        }

        
        $riwayat = $query->paginate(15)->appends($request->except('page', 'print'));

        
        $kelas = Kelas::all();
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('admin.riwayat.index', compact('riwayat', 'kelas', 'bulanList', 'tahunList', 'tahunDipilih'));
    }

  
    public function downloadNota(Pembayaran $pembayaran)
    {
        $pembayaran->load(['siswa.kelas', 'spp', 'petugas']);
        return view('admin.riwayat.nota', compact('pembayaran'));
    }
    
    public function downloadNotaTahun(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'tahun' => 'required|integer',
        ]);
        $siswa = Siswa::with('kelas')->findOrFail($request->id_siswa);
        $tahun = (int) $request->tahun;
        $bulanList = [
            'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];
        $spp = Spp::where('tahun', $tahun)->first() ?? Spp::latest()->first();
        $nominal = $spp ? (int) $spp->nominal : 0;
        $data = [];
        foreach ($bulanList as $bulan) {
            $p = Pembayaran::with(['spp','petugas'])
                ->where('id_siswa', $siswa->id)
                ->where('tahun_dibayar', $tahun)
                ->where('bulan_dibayar', $bulan)
                ->orderBy('tgl_bayar','desc')
                ->first();
            $jumlah = $p->jumlah_bayar ?? 0;
            $status = $p ? ($p->status ?? ($jumlah >= $nominal ? 'Lunas' : 'Belum Lunas')) : 'Belum Lunas';
            $data[] = [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nominal' => $nominal,
                'jumlah_bayar' => $jumlah,
                'sisa' => max(0, $nominal - $jumlah),
                'status' => $status,
                'pembayaran' => $p,
            ];
        }
        return view('admin.riwayat.nota_tahun', [
            'siswa' => $siswa,
            'tahun' => $tahun,
            'data' => $data,
            'spp' => $spp,
        ]);
    }
}
