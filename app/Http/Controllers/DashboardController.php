<?php

namespace App\Http\Controllers;

use App\Models\Khs;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET /dashboard/super-admin
    public function superAdmin()
    {
        return response()->json([
            'total_user' => User::count(),
            'total_fakultas' => Fakultas::count(),
            'total_prodi' => Prodi::count(),
            'total_mahasiswa' => User::whereHas('role', fn($q) => $q->where('nama_role', 'Mahasiswa'))->count(),
            'total_dosen' => User::whereHas('role', fn($q) => $q->where('nama_role', 'Dosen'))->count(),
        ]);
    }

    // GET /dashboard/fakultas/{id}
    public function fakultas($id)
    {
        $prodiIds = Prodi::where('fakultas_id', $id)->pluck('id');
        return response()->json([
            'total_prodi' => $prodiIds->count(),
            'total_dosen' => User::where('fakultas_id', $id)
                ->whereHas('role', fn($q) => $q->where('nama_role', 'Dosen'))->count(),
            'total_mahasiswa' => User::where('fakultas_id', $id)
                ->whereHas('role', fn($q) => $q->where('nama_role', 'Mahasiswa'))->count(),
        ]);
    }

    // GET /dashboard/prodi/{id}
    public function prodi($id)
    {
        return response()->json([
            'total_jurusan' => Jurusan::where('prodi_id', $id)->count(),
            'total_mahasiswa' => User::where('prodi_id', $id)
                ->whereHas('role', fn($q) => $q->where('nama_role', 'Mahasiswa'))->count(),
            'total_dosen' => User::where('prodi_id', $id)
                ->whereHas('role', fn($q) => $q->where('nama_role', 'Dosen'))->count(),
        ]);
    }

    // GET /dashboard/dosen
    public function dosen(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'User tidak ditemukan'], 401);
        }
        return response()->json([
            'jumlah_kelas_diampu' => JadwalKuliah::where('dosen_id', $user->id)->count(),
            'jumlah_mahasiswa_bimbingan' => User::where('role_id', function($q){
                $q->select('id')->from('roles')->where('nama_role', 'Mahasiswa');
            })->where('prodi_id', $user->prodi_id)->count(),
        ]);
    }

    // GET /dashboard/mahasiswa
    public function mahasiswa(Request $request)
    {
        $user = $request->user();
        $ipk = Khs::where('mahasiswa_id', $user->id)->avg('ip_semester');
        $sks = Khs::where('mahasiswa_id', $user->id)->sum('sks_semester');

        return response()->json([
            'ipk' => round($ipk, 2),
            'sks' => $sks,
            'notifikasi' => [
                'Jadwal KRS sudah dibuka',
                'Upload KHS semester terakhir',
            ],
        ]);
    }

}
