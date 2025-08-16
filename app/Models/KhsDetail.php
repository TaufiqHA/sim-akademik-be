<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KhsDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'khs_id',
        'mata_kuliah_id',
        'nilai_huruf',
        'nilai_angka'
    ];

    public function khs()
    {
        return $this->belongsTo(Khs::class, 'khs_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

}
