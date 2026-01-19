<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::where('id_user', $user->id)->first();
        }

        $riwayat = collect();
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

            $riwayat = $query->orderBy('tgl_bayar', 'desc')->paginate(15)->appends($request->except('page'));

            $tahunList = Pembayaran::where('id_siswa', $siswa->id)
                ->select('tahun_dibayar')
                ->distinct()
                ->orderBy('tahun_dibayar')
                ->pluck('tahun_dibayar');
        }

        return view('user.pembayaran.index', compact('riwayat', 'bulanList', 'tahunList', 'siswa'));
    }

    public function create()
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::with('kelas')->where('id_user', $user->id)->first();
        }

        $spp = Spp::orderBy('tahun', 'desc')->get();

        return view('user.pembayaran.create', compact('siswa', 'spp'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::where('id_user', $user->id)->first();
        }

        if (!$siswa) {
            return redirect()->back()->withErrors([
                'siswa' => 'Data siswa belum terhubung dengan akun ini.',
            ]);
        }

        $validated = $request->validate([
            'id_spp' => 'required|exists:spp,id',
            'bulan_dibayar' => 'required|string',
            'tahun_dibayar' => 'required|integer',
            'tgl_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        $spp = Spp::find($validated['id_spp']);

        if ($validated['jumlah_bayar'] >= $spp->nominal) {
            $validated['status'] = 'Lunas';
        } else {
            $validated['status'] = 'Belum Lunas';
        }

        $validated['id_siswa'] = $siswa->id;
        $validated['id_petugas'] = $user->id;

        Pembayaran::create($validated);

        return redirect()->route('user.dashboard')->with('success', 'Pembayaran berhasil disimpan.');
    }
}
