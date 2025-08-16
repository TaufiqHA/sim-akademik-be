<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\MahasiswaProfile;

class MahasiswaProfileController extends Controller
{
    public function show($userId)
    {
        $user = User::with('mahasiswaProfile')->find($userId);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if (!$user->mahasiswaProfile) {
            return response()->json(['message' => 'Profil mahasiswa belum dibuat'], 404);
        }

        return response()->json($user->mahasiswaProfile, 200);
    }

    public function store(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->mahasiswaProfile) {
            return response()->json(['message' => 'Profil mahasiswa sudah ada, gunakan update'], 400);
        }

        $request->validate([
            'nim'           => 'required|string|max:20|unique:mahasiswa_profiles,nim',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string|max:255',
            'angkatan'      => 'required|integer',
            'status'        => 'required|in:Aktif,Nonaktif,Cuti,Lulus',
        ]);

        $profile = MahasiswaProfile::create([
            'user_id'       => $user->id,
            'nim'           => $request->nim,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'angkatan'      => $request->angkatan,
            'status'        => $request->status,
        ]);

        return response()->json([
            'message' => 'Profil mahasiswa berhasil dibuat',
            'data'    => $profile
        ], 201);
    }

    public function update(Request $request, $userId)
    {
        $profile = MahasiswaProfile::where('user_id', $userId)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profil mahasiswa tidak ditemukan'], 404);
        }

        $request->validate([
            'nim'           => 'sometimes|required|string|max:20|unique:mahasiswa_profiles,nim,' . $profile->id,
            'tanggal_lahir' => 'sometimes|required|date',
            'alamat'        => 'sometimes|required|string|max:255',
            'angkatan'      => 'sometimes|required|integer',
            'status'        => 'sometimes|required|in:Aktif,Nonaktif,Cuti,Lulus',
        ]);

        $profile->update($request->only(['nim', 'tanggal_lahir', 'alamat', 'angkatan', 'status']));

        return response()->json([
            'message' => 'Profil mahasiswa berhasil diperbarui',
            'data'    => $profile
        ], 200);
    }

}
