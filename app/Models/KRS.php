<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KRS extends Model
{
    protected $fillable = ['mahasiswa_id', 'tahun_akademik_id', 'status'];
    public function details() {
        return $this->hasMany(KRSDetail::class, 'krs_id');
    }

}
