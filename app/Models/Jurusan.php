<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $guarded = ['id'];

    public function prodi()
    {
        $this->belongsTo(Prodi::class, 'prodi_id');
    }
}
