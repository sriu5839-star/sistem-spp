<?php

namespace Database\Seeders;

use App\Models\Spp;
use Illuminate\Database\Seeder;

class SppSeeder extends Seeder
{
    public function run(): void
    {
        $spp = [
            ['tahun' => 2024, 'nominal' => 200000],
            ['tahun' => 2025, 'nominal' => 250000],
            ['tahun' => 2026, 'nominal' => 200000],
        ];

        foreach ($spp as $data) {
            // Mengecek berdasarkan 'tahun', jika ada maka update 'nominal'
            Spp::updateOrCreate(
                ['tahun' => $data['tahun']], 
                ['nominal' => $data['nominal']]
            );
        }
    }
}