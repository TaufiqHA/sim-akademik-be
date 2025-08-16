<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'fakultas', 'prodi'])->get();
        return response()->json($users, 200);
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:100',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'role_id'     => 'required|exists:roles,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'prodi_id'    => 'nullable|exists:prodis,id',
        ]);

        $user = User::create([
            'nama'        => $request->nama,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role_id'     => $request->role_id,
            'fakultas_id' => $request->fakultas_id,
            'prodi_id'    => $request->prodi_id,
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data'    => $user->load(['role', 'fakultas', 'prodi'])
        ], 201);
    }

    /**
     * Detail user.
     */
    public function show($id)
    {
        $user = User::with(['role', 'fakultas', 'prodi'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json($user, 200);
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $request->validate([
            'nama'        => 'sometimes|required|string|max:100',
            'email'       => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password'    => 'nullable|string|min:6',
            'role_id'     => 'sometimes|required|exists:roles,id',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'prodi_id'    => 'nullable|exists:prodis,id',
        ]);

        $user->update([
            'nama'        => $request->nama ?? $user->nama,
            'email'       => $request->email ?? $user->email,
            'password'    => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'role_id'     => $request->role_id ?? $user->role_id,
            'fakultas_id' => $request->fakultas_id ?? $user->fakultas_id,
            'prodi_id'    => $request->prodi_id ?? $user->prodi_id,
        ]);

        return response()->json([
            'message' => 'User berhasil diperbarui',
            'data'    => $user->load(['role', 'fakultas', 'prodi'])
        ], 200);
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }

}
