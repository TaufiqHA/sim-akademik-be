<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DosenProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nidn',
        'jabatan',
        'keahlian',
        'status',
    ];

    /**
     * Relasi ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
