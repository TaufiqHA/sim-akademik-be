<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriKuliah extends Model
{
    protected $fillable = [
        'jadwal_kuliah_id',
        'judul',
        'deskripsi',
        'file_path',
        'uploaded_by'
    ];

    public function jadwalKuliah()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_kuliah_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

}
