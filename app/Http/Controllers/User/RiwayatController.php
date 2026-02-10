<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::where('id_user', $user->id)->first();
        }

        $pembayaran = collect();
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tahunList = collect();

        if ($siswa) {
            $query = Pembayaran::with(['spp'])
                ->where('id_siswa', $siswa->id);

            if ($request->filled('bulan')) {
                $query->where('bulan_dibayar', $request->bulan);
            }

            if ($request->filled('tahun')) {
                $query->where('tahun_dibayar', (int) $request->tahun);
            }

            $pembayaran = $query->orderBy('tgl_bayar', 'desc')->paginate(15)->appends($request->except('page'));

            $tahunList = Pembayaran::where('id_siswa', $siswa->id)
                ->select('tahun_dibayar')
                ->distinct()
                ->orderBy('tahun_dibayar')
                ->pluck('tahun_dibayar');
        } else {
            $pembayaran = collect();
        }

        return view('user.riwayat.index', compact('pembayaran', 'bulanList', 'tahunList', 'siswa'));
    }

    public function nota(Pembayaran $pembayaran)
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::where('id_user', $user->id)->first();
        }

        if (!$siswa || $pembayaran->id_siswa !== $siswa->id) {
            return redirect()->route('user.riwayat.index')->with('error', 'Anda tidak memiliki akses ke nota ini.');
        }

        $pembayaran->load(['siswa.kelas', 'spp', 'petugas']);
        return view('user.riwayat.nota', compact('pembayaran'));
    }

    public function cetak(Request $request)
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::where('id_user', $user->id)->first();
        }

        if (!$siswa) {
            return redirect()->route('user.riwayat.index')->with('error', 'Data siswa belum terhubung.');
        }

        $query = Pembayaran::with(['spp', 'siswa.kelas'])
            ->where('id_siswa', $siswa->id);

        if ($request->filled('bulan')) {
            $query->where('bulan_dibayar', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_dibayar', (int) $request->tahun);
        }

        $riwayat = $query->orderBy('tgl_bayar', 'desc')->get();

        return view('user.riwayat.cetak', [
            'riwayat' => $riwayat,
            'siswa' => $siswa,
            'bulan' => $request->get('bulan'),
            'tahun' => $request->get('tahun'),
        ]);
    }
}
