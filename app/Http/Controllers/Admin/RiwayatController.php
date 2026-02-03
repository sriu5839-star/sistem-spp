<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
   
    public function index(Request $request)
    {
        
        $query = Siswa::with(['kelas', 'pembayaran']);

        
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

        return view('admin.riwayat.index', compact('riwayat', 'kelas', 'bulanList'));
    }

  
    public function downloadNota(Pembayaran $pembayaran)
    {
        $pembayaran->load(['siswa.kelas', 'spp', 'petugas']);
        return view('admin.riwayat.nota', compact('pembayaran'));
    }
}
