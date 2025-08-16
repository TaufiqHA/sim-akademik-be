<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'jadwal_kuliah_id',
        'tugas',
        'uts',
        'uas',
        'nilai_akhir'
    ];

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_kuliah_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }


}
