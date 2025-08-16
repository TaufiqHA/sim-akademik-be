<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DosenProfile;
use Illuminate\Http\Request;

class DosenProfileController extends Controller
{
    public function show($userId)
    {
        $user = User::with('dosenProfile')->find($userId);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if (!$user->dosenProfile) {
            return response()->json(['message' => 'Profil dosen belum dibuat'], 404);
        }

        return response()->json($user->dosenProfile, 200);
    }

    /**
     * Simpan profil dosen baru.
     */
    public function store(Request $request, $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->dosenProfile) {
            return response()->json(['message' => 'Profil dosen sudah ada, gunakan update'], 400);
        }

        $request->validate([
            'nidn'     => 'required|string|max:20|unique:dosen_profiles,nidn',
            'jabatan'  => 'required|string|max:100',
        ]);

        $profile = DosenProfile::create([
            'user_id'  => $user->id,
            'nidn'     => $request->nidn,
            'jabatan'  => $request->jabatan,
        ]);

        return response()->json([
            'message' => 'Profil dosen berhasil dibuat',
            'data'    => $profile
        ], 201);
    }

    /**
     * Update profil dosen.
     */
    public function update(Request $request, $userId)
    {
        $profile = DosenProfile::where('user_id', $userId)->first();

        if (!$profile) {
            return response()->json(['message' => 'Profil dosen tidak ditemukan'], 404);
        }

        $request->validate([
            'nidn'     => 'sometimes|required|string|max:20|unique:dosen_profiles,nidn,' . $profile->id,
            'jabatan'  => 'sometimes|required|string|max:100',
        ]);

        $profile->update($request->only(['nidn', 'jabatan']));

        return response()->json([
            'message' => 'Profil dosen berhasil diperbarui',
            'data'    => $profile
        ], 200);
    }

}
