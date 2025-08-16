<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaProfile extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }
}
