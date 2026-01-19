<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelas = [
            // Kelas X
            [
                'nama_kelas' => 'X RPL 1',
                'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'nama_kelas' => 'X RPL 2',
                'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'nama_kelas' => 'X TKJ 1',
                'kompetensi_keahlian' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'nama_kelas' => 'X TKJ 2',
                'kompetensi_keahlian' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'nama_kelas' => 'X MM 1',
                'kompetensi_keahlian' => 'Multimedia',
            ],
            // Kelas XI
            [
                'nama_kelas' => 'XI RPL 1',
                'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'nama_kelas' => 'XI RPL 2',
                'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'nama_kelas' => 'XI TKJ 1',
                'kompetensi_keahlian' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'nama_kelas' => 'XI TKJ 2',
                'kompetensi_keahlian' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'nama_kelas' => 'XI MM 1',
                'kompetensi_keahlian' => 'Multimedia',
            ],
            // Kelas XII
            [
                'nama_kelas' => 'XII RPL 1',
                'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'nama_kelas' => 'XII RPL 2',
                'kompetensi_keahlian' => 'Rekayasa Perangkat Lunak',
            ],
            [
                'nama_kelas' => 'XII TKJ 1',
                'kompetensi_keahlian' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'nama_kelas' => 'XII TKJ 2',
                'kompetensi_keahlian' => 'Teknik Komputer dan Jaringan',
            ],
            [
                'nama_kelas' => 'XII MM 1',
                'kompetensi_keahlian' => 'Multimedia',
            ],
        ];

        foreach ($kelas as $data) {
            Kelas::create($data);
        }
    }
}

