<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Yudisium extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'ipk',
        'nilai_sidang',
        'pembimbing_id',
        'penguji_id',
        'status',
        'alasan_reject',
    ];

    /**
     * Relasi ke mahasiswa (user)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke pembimbing (user)
     */
    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    /**
     * Relasi ke penguji (user)
     */
    public function penguji()
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }

}
