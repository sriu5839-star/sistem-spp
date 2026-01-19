<?php

namespace Database\Seeders;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PembayaranSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan data yang diperlukan sudah ada
        $siswa = Siswa::all();
        $spp = Spp::all();
        $petugas = User::where('role', 'petugas')->get();
        $admin = User::where('role', 'admin')->first();

        if ($siswa->isEmpty()) {
            $this->command->warn('Siswa belum ada. Jalankan SiswaSeeder terlebih dahulu.');
            return;
        }

        if ($spp->isEmpty()) {
            $this->command->warn('SPP belum ada. Jalankan SppSeeder terlebih dahulu.');
            return;
        }

        if ($petugas->isEmpty()) {
            $this->command->warn('Petugas belum ada. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Ambil SPP untuk tahun 2024 dan 2025
        $spp2024 = $spp->where('tahun', 2024)->first();
        $spp2025 = $spp->where('tahun', 2025)->first();

        if (!$spp2024 || !$spp2025) {
            $this->command->warn('SPP untuk tahun 2024 atau 2025 belum ada.');
            return;
        }

        // Buat pembayaran untuk tahun 2024 (12 bulan untuk semua siswa)
        $this->command->info('Membuat pembayaran untuk tahun 2024...');
        foreach ($siswa as $index => $s) {
            $petugasRandom = $petugas->random();
            
            foreach ($bulan as $bulanIndex => $bulanDibayar) {
                // Simulasi beberapa siswa belum bayar beberapa bulan
                if ($index < 5 && $bulanIndex >= 10) {
                    // 5 siswa pertama belum bayar bulan November dan Desember
                    continue;
                }
                
                // Simulasi beberapa siswa bayar kurang dari nominal
                $jumlahBayar = $spp2024->nominal;
                if ($index >= 5 && $index < 10 && $bulanIndex >= 8) {
                    // 5 siswa berikutnya bayar kurang untuk bulan September, Oktober, November, Desember
                    $jumlahBayar = $spp2024->nominal * 0.7; // Bayar 70%
                }
                
                $status = $jumlahBayar >= $spp2024->nominal ? 'Lunas' : 'Belum Lunas';
                
                // Tanggal pembayaran: bulan yang sesuai di tahun 2024
                $tglBayar = Carbon::create(2024, $bulanIndex + 1, rand(1, 28));
                
                Pembayaran::create([
                    'id_siswa' => $s->id,
                    'id_spp' => $spp2024->id,
                    'id_petugas' => $petugasRandom->id,
                    'bulan_dibayar' => $bulanDibayar,
                    'tahun_dibayar' => 2024,
                    'tgl_bayar' => $tglBayar,
                    'jumlah_bayar' => $jumlahBayar,
                    'status' => $status,
                ]);
            }
        }

        // Buat pembayaran untuk tahun 2025 (beberapa bulan untuk beberapa siswa)
        $this->command->info('Membuat pembayaran untuk tahun 2025...');
        foreach ($siswa->take(25) as $index => $s) {
            $petugasRandom = $petugas->random();
            
            // Untuk tahun 2025, buat pembayaran untuk 6 bulan pertama
            foreach (array_slice($bulan, 0, 6) as $bulanIndex => $bulanDibayar) {
                // Simulasi beberapa siswa belum bayar
                if ($index >= 20 && $bulanIndex >= 4) {
                    // 5 siswa terakhir belum bayar bulan Mei dan Juni
                    continue;
                }
                
                // Simulasi beberapa siswa bayar kurang
                $jumlahBayar = $spp2025->nominal;
                if ($index >= 15 && $index < 20 && $bulanIndex >= 3) {
                    // 5 siswa bayar kurang untuk bulan April, Mei, Juni
                    $jumlahBayar = $spp2025->nominal * 0.8; // Bayar 80%
                }
                
                $status = $jumlahBayar >= $spp2025->nominal ? 'Lunas' : 'Belum Lunas';
                
                // Tanggal pembayaran: bulan yang sesuai di tahun 2025
                $tglBayar = Carbon::create(2025, $bulanIndex + 1, rand(1, 28));
                
                Pembayaran::create([
                    'id_siswa' => $s->id,
                    'id_spp' => $spp2025->id,
                    'id_petugas' => $petugasRandom->id,
                    'bulan_dibayar' => $bulanDibayar,
                    'tahun_dibayar' => 2025,
                    'tgl_bayar' => $tglBayar,
                    'jumlah_bayar' => $jumlahBayar,
                    'status' => $status,
                ]);
            }
        }

        $this->command->info('Pembayaran berhasil dibuat untuk 2 tahun (2024 dan 2025).');
    }
}

