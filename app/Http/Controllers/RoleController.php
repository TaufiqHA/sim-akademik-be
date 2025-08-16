<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return response()->json(Role::all(), 200);
    }

    /**
     * Simpan role baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role',
        ]);

        $role = Role::create([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json([
            'message' => 'Role berhasil dibuat',
            'data' => $role
        ], 201);
    }

    /**
     * Tampilkan detail role.
     */
    public function show($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], 404);
        }

        return response()->json($role, 200);
    }

    /**
     * Update role.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_role' => 'required|string|max:50|unique:roles,nama_role,' . $role->id,
        ]);

        $role->update([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json([
            'message' => 'Role berhasil diperbarui',
            'data' => $role
        ], 200);
    }

    /**
     * Hapus role.
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role tidak ditemukan'], 404);
        }

        $role->delete();

        return response()->json(['message' => 'Role berhasil dihapus'], 200);
    }

}
