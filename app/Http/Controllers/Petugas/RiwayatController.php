<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /**
     * Menampilkan riwayat pembayaran (hanya yang dibuat oleh petugas ini)
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['siswa.kelas', 'spp'])
            ->where('id_petugas', Auth::id());

        // Search berdasarkan nama siswa atau NISN
        if ($request->has('search') && $request->search) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && $request->kelas) {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas);
            });
        }

        // Filter berdasarkan bulan
        if ($request->has('bulan') && $request->bulan) {
            $query->where('bulan_dibayar', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && $request->tahun) {
            $query->where('tahun_dibayar', $request->tahun);
        }

        $riwayat = $query->orderBy('tgl_bayar', 'desc')->paginate(15);
        $kelas = Kelas::all();

        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('petugas.riwayat.index', compact('riwayat', 'kelas', 'bulanList'));
    }

    /**
     * Download/cetak nota pembayaran
     */
    public function downloadNota(Pembayaran $pembayaran)
    {
        // Pastikan pembayaran ini dibuat oleh petugas yang login
        if ($pembayaran->id_petugas !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke nota ini.');
        }

        $pembayaran->load(['siswa.kelas', 'spp', 'petugas']);
        return view('petugas.riwayat.nota', compact('pembayaran'));
    }
}

