<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model
     */
    protected $table = 'pembayaran';

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_siswa',
        'id_spp',
        'id_petugas',
        'bulan_dibayar',
        'tahun_dibayar',
        'tgl_bayar',
        'jumlah_bayar',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tgl_bayar' => 'date',
            'jumlah_bayar' => 'decimal:2',
            'tahun_dibayar' => 'integer',
        ];
    }

    /**
     * Relasi ke model Siswa
     * Satu pembayaran belongs to satu siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    /**
     * Relasi ke model Spp
     * Satu pembayaran belongs to satu SPP
     */
    public function spp(): BelongsTo
    {
        return $this->belongsTo(Spp::class, 'id_spp');
    }

    /**
     * Relasi ke model User (Petugas)
     * Satu pembayaran belongs to satu petugas
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }
}

