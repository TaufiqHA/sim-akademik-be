<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKuliah extends Model
{
    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'ruang',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'tahun_akademik_id',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

}
