<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Fakultas;
use App\Models\TahunAkademik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Seed Roles ---
        $roles = [
            'Super Admin',
            'Dekan',
            'TU Fakultas',
            'Kaprodi',
            'TU Prodi',
            'Dosen',
            'Mahasiswa',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nama_role' => $role]);
        }

        // --- Seed Fakultas ---
        $fakultasTeknik = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Teknik']);
        $fakultasEkonomi = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Ekonomi']);

        // --- Seed Prodi ---
        $prodiTI = Prodi::firstOrCreate([
            'fakultas_id' => $fakultasTeknik->id,
            'nama_prodi'  => 'Teknik Informatika',
        ]);

        $prodiManajemen = Prodi::firstOrCreate([
            'fakultas_id' => $fakultasEkonomi->id,
            'nama_prodi'  => 'Manajemen',
        ]);

        // --- Seed Tahun Akademik ---
        TahunAkademik::firstOrCreate([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
        ], [
            'is_aktif' => true,
            'periode_krs_mulai'     => now()->addDays(7),
            'periode_krs_selesai'   => now()->addDays(14),
            'periode_nilai_mulai'   => now()->addMonths(4),
            'periode_nilai_selesai' => now()->addMonths(5),
        ]);

        // --- Seed Users (1 user untuk setiap role) ---
        $userData = [
            'Super Admin' => [
                'nama' => 'Super Administrator',
                'email' => 'superadmin@simak.test',
                'fakultas_id' => null,
                'prodi_id' => null,
            ],
            'Dekan' => [
                'nama' => 'Dekan FT',
                'email' => 'dekan@simak.test',
                'fakultas_id' => $fakultasTeknik->id,
                'prodi_id' => null,
            ],
            'TU Fakultas' => [
                'nama' => 'TU Fakultas Ekonomi',
                'email' => 'tufakultas@simak.test',
                'fakultas_id' => $fakultasEkonomi->id,
                'prodi_id' => null,
            ],
            'Kaprodi' => [
                'nama' => 'Kaprodi TI',
                'email' => 'kaprodi@simak.test',
                'fakultas_id' => $fakultasTeknik->id,
                'prodi_id' => $prodiTI->id,
            ],
            'TU Prodi' => [
                'nama' => 'TU Prodi Manajemen',
                'email' => 'tuprodi@simak.test',
                'fakultas_id' => $fakultasEkonomi->id,
                'prodi_id' => $prodiManajemen->id,
            ],
            'Dosen' => [
                'nama' => 'Budi Dosen',
                'email' => 'dosen@simak.test',
                'fakultas_id' => $fakultasTeknik->id,
                'prodi_id' => $prodiTI->id,
            ],
            'Mahasiswa' => [
                'nama' => 'Andi Mahasiswa',
                'email' => 'mahasiswa@simak.test',
                'fakultas_id' => $fakultasEkonomi->id,
                'prodi_id' => $prodiManajemen->id,
            ],
        ];

        foreach ($userData as $roleName => $data) {
            $role = Role::where('nama_role', $roleName)->first();

            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nama'        => $data['nama'],
                    'password'    => Hash::make('password123'),
                    'role_id'     => $role->id,
                    'fakultas_id' => $data['fakultas_id'],
                    'prodi_id'    => $data['prodi_id'],
                ]
            );
        }
    }

}
