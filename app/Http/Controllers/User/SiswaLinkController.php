<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class SiswaLinkController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        $siswa = null;
        if (Schema::hasColumn('siswa', 'id_user')) {
            $siswa = Siswa::with('kelas')->where('id_user', $user->id)->first();
        }

        return view('user.siswa.link', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|exists:siswa,nisn',
        ]);

        $user = Auth::user();

        if (!Schema::hasColumn('siswa', 'id_user')) {
            return redirect()->back()->withErrors([
                'nisn' => 'Sistem belum mendukung penghubungan akun dengan data siswa.',
            ]);
        }

        $siswa = Siswa::where('nisn', $request->nisn)->first();

        if (!$siswa) {
            return redirect()->back()->withErrors([
                'nisn' => 'Data siswa tidak ditemukan.',
            ])->withInput();
        }

        if (!is_null($siswa->id_user) && $siswa->id_user !== $user->id) {
            return redirect()->back()->withErrors([
                'nisn' => 'Data siswa ini sudah terhubung dengan akun lain.',
            ])->withInput();
        }

        $siswa->id_user = $user->id;
        $siswa->save();

        return redirect()->route('user.dashboard')->with('success', 'Data siswa berhasil dihubungkan.');
    }
}
