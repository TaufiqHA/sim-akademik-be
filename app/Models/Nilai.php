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

}
