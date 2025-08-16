<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use Illuminate\Http\Request;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::all();
        return response()->json($fakultas, 200);
    }

    /**
     * Simpan fakultas baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_fakultas' => 'required|string|max:100|unique:fakultas,nama',
        ]);

        $fakultas = Fakultas::create([
            'nama_fakultas' => $request->nama_fakultas,
        ]);

        return response()->json([
            'message' => 'Fakultas berhasil dibuat',
            'data'    => $fakultas
        ], 201);
    }

    /**
     * Tampilkan detail fakultas.
     */
    public function show($id)
    {
        $fakultas = Fakultas::find($id);

        if (!$fakultas) {
            return response()->json(['message' => 'Fakultas tidak ditemukan'], 404);
        }

        return response()->json($fakultas, 200);
    }

    /**
     * Update fakultas.
     */
    public function update(Request $request, $id)
    {
        $fakultas = Fakultas::find($id);

        if (!$fakultas) {
            return response()->json(['message' => 'Fakultas tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_fakultas' => 'sometimes|required|string|max:100|unique:fakultas,nama,' . $id,
        ]);

        $fakultas->update($request->only(['nama_fakultas']));

        return response()->json([
            'message' => 'Fakultas berhasil diperbarui',
            'data'    => $fakultas
        ], 200);
    }

    /**
     * Hapus fakultas.
     */
    public function destroy($id)
    {
        $fakultas = Fakultas::find($id);

        if (!$fakultas) {
            return response()->json(['message' => 'Fakultas tidak ditemukan'], 404);
        }

        $fakultas->delete();

        return response()->json(['message' => 'Fakultas berhasil dihapus'], 200);
    }

}
