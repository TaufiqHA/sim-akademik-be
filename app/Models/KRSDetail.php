<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KRSDetail extends Model
{
    protected $fillable = ['krs_id', 'jadwal_kuliah_id'];
    public function krs() {
        return $this->belongsTo(KRS::class, 'krs_id');
    }

}
