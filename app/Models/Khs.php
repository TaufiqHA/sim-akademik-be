<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Khs extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'tahun_akademik_id',
        'ip_semester',
        'sks_semester'
    ];

    public function details()
    {
        return $this->hasMany(KhsDetail::class, 'khs_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

}
