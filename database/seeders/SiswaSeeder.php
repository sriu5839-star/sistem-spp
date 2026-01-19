<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiswaSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan kelas sudah ada
        $kelas = Kelas::all();
        
        if ($kelas->isEmpty()) {
            $this->command->warn('Kelas belum ada. Jalankan KelasSeeder terlebih dahulu.');
            return;
        }

        // Buat 50 siswa dengan distribusi ke kelas yang berbeda
        // Setiap kelas akan mendapat sekitar 3-4 siswa
        foreach ($kelas as $k) {
            Siswa::factory(rand(3, 4))->create([
                'id_kelas' => $k->id,
            ]);
        }
    }
}

