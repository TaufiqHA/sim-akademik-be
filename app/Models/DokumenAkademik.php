<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DokumenAkademik extends Model
{
    protected $fillable = [
        'jenis_dokumen',
        'file_path',
        'uploaded_by',
        'approved_by',
        'status',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
