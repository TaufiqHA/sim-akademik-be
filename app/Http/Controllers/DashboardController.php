<?php

namespace App\Http\Controllers;

use App\Models\Khs;
use App\Models\User;
use App\Models\Prodi;
use App\Models\Jurusan;
use App\Models\Fakultas;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function getDashboardTuProdi($id)
    {
        // Pastikan ID prodi valid
        $prodiId = (int) $id;

        // Total Mahasiswa di Prodi
        $totalMahasiswa = DB::table('users')
            ->where('role_id', function ($query) {
                $query->select('id')
                      ->from('roles')
                      ->where('nama_role', 'Mahasiswa');
            })
            ->where('prodi_id', $prodiId)
            ->count();

        // Total Dosen di Prodi
        $totalDosen = DB::table('users')
            ->where('role_id', function ($query) {
                $query->select('id')
                      ->from('roles')
                      ->where('nama_role', 'Dosen');
            })
            ->where('prodi_id', $prodiId)
            ->count();

        // KRS Pending (Submitted, belum Approved)
        $krsPending = DB::table('k_r_s')
            ->join('users', 'k_r_s.mahasiswa_id', '=', 'users.id')
            ->where('users.prodi_id', $prodiId)
            ->where('k_r_s.status', 'Submitted')
            ->count();

        // Surat Pending (dokumen akademik dengan status Pending)
        $suratPending = DB::table('dokumen_akademiks')
            ->join('users', 'dokumen_akademiks.uploaded_by', '=', 'users.id')
            ->where('users.prodi_id', $prodiId)
            ->where('dokumen_akademiks.status', 'Pending')
            ->count();

        // Jadwal Kuliah untuk Prodi
        $jadwalKuliah = DB::table('jadwal_kuliahs')
            ->join('mata_kuliahs', 'jadwal_kuliahs.mata_kuliah_id', '=', 'mata_kuliahs.id')
            ->where('mata_kuliahs.prodi_id', $prodiId)
            ->count();

        return response()->json([
            'total_mahasiswa' => $totalMahasiswa,
            'krs_pending' => $krsPending,
            'surat_pending' => $suratPending,
            'total_dosen' => $totalDosen,
            'jadwal_kuliah' => $jadwalKuliah,
        ]);
    }


}
