<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUjian extends Model
{
    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'ruang',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jenis_ujian',
        'tahun_akademik_id'
    ];

    // Relasi ke Mata Kuliah
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    // Relasi ke Dosen (User)
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

}
