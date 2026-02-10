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
    public function lunaskanTahun(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'tahun' => 'required|integer',
        ]);
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $spp = Spp::where('tahun', $request->tahun)->first() ?? Spp::latest()->first();
        $nominal = $spp ? (int) $spp->nominal : 0;
        foreach ($months as $month) {
            $p = Pembayaran::where('id_siswa', $request->id_siswa)
                ->where('tahun_dibayar', $request->tahun)
                ->where('bulan_dibayar', $month)
                ->orderBy('tgl_bayar', 'desc')
                ->first();
            if (!$p) {
                Pembayaran::create([
                    'id_petugas' => Auth::id(),
                    'id_siswa' => $request->id_siswa,
                    'tgl_bayar' => now(),
                    'bulan_dibayar' => $month,
                    'tahun_dibayar' => $request->tahun,
                    'id_spp' => $spp ? $spp->id : null,
                    'jumlah_bayar' => $nominal,
                    'status' => 'Lunas',
                ]);
            } else {
                if (($p->status ?? '') !== 'Lunas' || (int) ($p->jumlah_bayar ?? 0) < $nominal) {
                    $p->jumlah_bayar = $nominal;
                    $p->status = 'Lunas';
                    $p->tgl_bayar = now();
                    $p->id_petugas = Auth::id();
                    $p->save();
                }
            }
        }
        return redirect()->route('admin.pembayaran.create', [
            'id_siswa' => $request->id_siswa,
            'tahun' => $request->tahun,
        ])->with('success', 'Semua bulan tahun ' . $request->tahun . ' telah dilunaskan.');
    }
    public function checkStatus(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|integer|exists:siswa,id',
            'tahun' => 'required|integer',
            'bulan' => 'required|string',
        ]);

        $spp = Spp::where('tahun', $request->tahun)->first() ?? Spp::latest()->first();
        $p = Pembayaran::where('id_siswa', $request->id_siswa)
            ->where('tahun_dibayar', $request->tahun)
            ->where('bulan_dibayar', $request->bulan)
            ->orderBy('tgl_bayar', 'desc')
            ->first();

        $jumlah = $p->jumlah_bayar ?? 0;
        $nominal = $spp->nominal ?? 0;
        $status = $p ? ($p->status ?? ($jumlah >= $nominal ? 'Lunas' : 'Belum Lunas')) : 'Belum Lunas';
        $sisa = max(0, $nominal - $jumlah);

        return response()->json([
            'exists' => (bool) $p,
            'status' => $status,
            'nominal' => $nominal,
            'jumlah_bayar' => $jumlah,
            'sisa' => $sisa,
            'id_spp' => $spp->id ?? null,
            'pembayaran_id' => $p->id ?? null,
        ]);
    }
    public function create(Request $request)
    {
        $siswaList = Siswa::all(); 
        $sppList = Spp::orderBy('tahun', 'asc')->get();
        $selectedSiswa = null;
        $tagihan = [];

        if ($request->has('id_siswa')) {
            $selectedSiswa = Siswa::find($request->id_siswa);
        } else {
            $selectedSiswa = Siswa::first();
        }

        if ($selectedSiswa) {
                $years = $sppList->pluck('tahun')->toArray();
                if (empty($years)) {
                    $years = [now()->year];
                }
                $tahunFilter = $request->get('tahun') ? (int) $request->get('tahun') : (int) last($years);
                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                $sppData = Spp::whereIn('tahun', $years)->get()->keyBy('tahun'); 

                $year = $tahunFilter;
                $nominal = $sppData[$year]->nominal ?? 0;
                if ($nominal == 0) {
                    $lastSpp = Spp::latest()->first();
                    $nominal = $lastSpp ? $lastSpp->nominal : 0;
                }

                foreach ($months as $index => $month) {
                    $pembayaran = Pembayaran::where('id_siswa', $selectedSiswa->id)
                                            ->where('tahun_dibayar', $year)
                                            ->where('bulan_dibayar', $month)
                                            ->orderBy('tgl_bayar', 'desc')
                                            ->first();

                    $status = $pembayaran ? ($pembayaran->status ?? 'Lunas') : 'Belum Lunas';
                    $terbayar = $pembayaran ? (int) $pembayaran->jumlah_bayar : 0;
                    $sisa = max(0, (int) $nominal - $terbayar);
                    
                    $tagihan[] = [
                        'tahun' => $year,
                        'bulan' => $month,
                        'nominal' => $nominal,
                        'status' => $status,
                        'is_paid' => (bool)$pembayaran,
                        'pembayaran_id' => $pembayaran->id ?? null,
                        'terbayar' => $terbayar,
                        'sisa' => $sisa,
                    ];
                }
        }

        $firstUnpaidMonth = null;
        if (!empty($tagihan)) {
            foreach ($tagihan as $t) {
                if ($t['status'] !== 'Lunas') {
                    $firstUnpaidMonth = $t['bulan'];
                    break;
                }
            }
        }

        $tahunList = $sppList->pluck('tahun')->values();
        $tahunFilter = isset($year) ? $year : (int) (count($tahunList) ? $tahunList->last() : now()->year);
        return view('admin.pembayaran.create', compact('siswaList', 'selectedSiswa', 'tagihan', 'sppList', 'tahunList', 'tahunFilter', 'firstUnpaidMonth'));
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

        return redirect()
            ->route('admin.pembayaran.create', [
                'id_siswa' => $request->id_siswa,
                'tahun' => $request->tahun_dibayar,
            ])
            ->with('success', $status === 'Lunas' ? 'Pembayaran berhasil, status: Sudah Lunas' : 'Pembayaran tercatat, status: Belum Lunas');
    }
}
