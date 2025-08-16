<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role_id',
        'fakultas_id',
        'prodi_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke Fakultas.
     */
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class);
    }

    /**
     * Relasi ke Prodi.
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    /**
     * Relasi ke profil mahasiswa.
     */
    // public function mahasiswaProfile()
    // {
    //     return $this->hasOne(MahasiswaProfile::class);
    // }

    // /**
    //  * Relasi ke profil dosen.
    //  */
    // public function dosenProfile()
    // {
    //     return $this->hasOne(DosenProfile::class);
    // }

    // /**
    //  * Relasi ke dokumen akademik yang diupload.
    //  */
    // public function dokumenAkademik()
    // {
    //     return $this->hasMany(DokumenAkademik::class, 'uploaded_by');
    // }

    // /**
    //  * Relasi ke dokumen akademik yang diapprove.
    //  */
    // public function dokumenDisetujui()
    // {
    //     return $this->hasMany(DokumenAkademik::class, 'approved_by');
    // }

}
