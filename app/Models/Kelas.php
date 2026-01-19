<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model
     */
    protected $table = 'kelas';

    /**
     * Atribut yang dapat diisi secara massal
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kelas',
        'kompetensi_keahlian',
    ];

    /**
     * Relasi ke model Siswa
     * Satu kelas memiliki banyak siswa
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'id_kelas');
    }
}

