<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kompetensi = [
            'Teknik Komputer dan Jaringan',
            'Rekayasa Perangkat Lunak',
            'Multimedia',
            'Teknik Kendaraan Ringan',
            'Teknik Sepeda Motor',
            'Akuntansi',
            'Administrasi Perkantoran',
            'Pemasaran',
        ];

        return [
            'nama_kelas' => fake()->randomElement(['X', 'XI', 'XII']) . ' ' . fake()->randomElement(['A', 'B', 'C', 'D']),
            'kompetensi_keahlian' => fake()->randomElement($kompetensi),
        ];
    }
}

