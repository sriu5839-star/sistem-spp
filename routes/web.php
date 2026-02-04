<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Auth\LoginController;

// Default route mengarah ke halaman login
Route::middleware('guest')->get('/', [LoginController::class, 'create']);

// Dashboard Routes (Protected by auth middleware)
Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Data Siswa
        Route::resource('siswa', App\Http\Controllers\Admin\SiswaController::class);
        
        // Data Kelas
        Route::resource('kelas', App\Http\Controllers\Admin\KelasController::class)->parameters(['kelas' => 'kelas']);
        
        // Data SPP
        Route::resource('spp', App\Http\Controllers\Admin\SppController::class);
        
        // Pembayaran
        Route::get('/pembayaran/check-status', [App\Http\Controllers\Admin\PembayaranController::class, 'checkStatus'])->name('pembayaran.check-status');
        Route::get('/pembayaran/create', [App\Http\Controllers\Admin\PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::post('/pembayaran', [App\Http\Controllers\Admin\PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::get('/pembayaran/search-siswa', [App\Http\Controllers\Admin\PembayaranController::class, 'searchSiswa'])->name('pembayaran.search-siswa');
        
        // Riwayat Pembayaran
        Route::get('/riwayat', [App\Http\Controllers\Admin\RiwayatController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{pembayaran}/nota', [App\Http\Controllers\Admin\RiwayatController::class, 'downloadNota'])->name('riwayat.nota');
        
        // Rekap Kelas
        Route::get('/rekap', [App\Http\Controllers\Admin\RekapController::class, 'index'])->name('rekap.index');
    });
    
    // Petugas Routes
    Route::prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Petugas\DashboardController::class, 'index'])->name('dashboard');
        
        // Pembayaran
        Route::get('/pembayaran/create', [App\Http\Controllers\Petugas\PembayaranController::class, 'create'])->name('pembayaran.create');
        Route::post('/pembayaran', [App\Http\Controllers\Petugas\PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::get('/pembayaran/search-siswa', [App\Http\Controllers\Petugas\PembayaranController::class, 'searchSiswa'])->name('pembayaran.search-siswa');
        
        // Riwayat Pembayaran
        Route::get('/riwayat', [App\Http\Controllers\Petugas\RiwayatController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/{pembayaran}/nota', [App\Http\Controllers\Petugas\RiwayatController::class, 'downloadNota'])->name('riwayat.nota');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', function () {
            $user = Auth::user();

            $siswa = null;
            if (Schema::hasColumn('siswa', 'id_user')) {
                $siswa = \App\Models\Siswa::with('pembayaran')->where('id_user', $user->id)->first();
            }

            $bulanList = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            $tahunList = collect();
            $tahunDipilih = null;
            $pembayaranTahun = collect();

            if ($siswa) {
                $tahunList = $siswa->pembayaran->pluck('tahun_dibayar')->unique()->sort()->values();
                $tahunDipilih = request('tahun') ? (int) request('tahun') : $tahunList->last();

                if ($tahunDipilih) {
                    $pembayaranTahun = $siswa->pembayaran->where('tahun_dibayar', $tahunDipilih);
                }
            }

            return view('user.dashboard', [
                'siswa' => $siswa,
                'bulanList' => $bulanList,
                'tahunList' => $tahunList,
                'tahunDipilih' => $tahunDipilih,
                'pembayaranTahun' => $pembayaranTahun,
            ]);
        })->name('dashboard');

        

        Route::get('/siswa/link', [App\Http\Controllers\User\SiswaLinkController::class, 'create'])->name('siswa.link');
        Route::post('/siswa/link', [App\Http\Controllers\User\SiswaLinkController::class, 'store'])->name('siswa.link.store');

        Route::get('/riwayat', [App\Http\Controllers\User\RiwayatController::class, 'index'])->name('riwayat.index');
        Route::get('/riwayat/cetak', [App\Http\Controllers\User\RiwayatController::class, 'cetak'])->name('riwayat.cetak');
        Route::get('/riwayat/{pembayaran}/nota', [App\Http\Controllers\User\RiwayatController::class, 'nota'])->name('riwayat.nota');
    });
});
