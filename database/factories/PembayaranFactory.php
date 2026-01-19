<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pembayaran>
 */
class PembayaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tahun = fake()->numberBetween(2020, 2025);
        
        // Gunakan SPP yang sudah ada atau buat baru jika belum ada
        $spp = Spp::where('tahun', $tahun)->first() ?? Spp::factory()->create(['tahun' => $tahun]);
        
        // Gunakan petugas yang sudah ada atau buat baru jika belum ada
        $petugas = User::where('role', 'petugas')->first() ?? User::factory()->create(['role' => 'petugas']);

        return [
            'id_siswa' => Siswa::factory(),
            'id_spp' => $spp->id,
            'id_petugas' => $petugas->id,
            'bulan_dibayar' => fake()->randomElement($bulan),
            'tahun_dibayar' => $tahun,
            'tgl_bayar' => fake()->dateTimeBetween('-1 year', 'now'),
            'jumlah_bayar' => $spp->nominal,
            'status' => fake()->randomElement(['Lunas', 'Belum Lunas']),
        ];
    }
}

