<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MataKuliahController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MataKuliah::query();

        if ($request->has('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $perPage = $request->get('per_page', 15);
        $mataKuliah = $query->paginate($perPage);

        return response()->json($mataKuliah);
    }

    /**
     * Simpan mata kuliah baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kode_mk'   => 'required|string|max:20|unique:mata_kuliahs,kode_mk',
            'nama_mk'   => 'required|string|max:100',
            'sks'       => 'required|integer|min:1',
            'prodi_id'  => 'required|exists:prodis,id',
        ]);

        $mataKuliah = MataKuliah::create($validated);

        return response()->json($mataKuliah, 201);
    }

    /**
     * Detail mata kuliah
     */
    public function show(int $id): JsonResponse
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        return response()->json($mataKuliah);
    }

    /**
     * Update mata kuliah
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $mataKuliah = MataKuliah::findOrFail($id);

        $validated = $request->validate([
            'kode_mk'   => 'sometimes|string|max:20|unique:mata_kuliah,kode_mk,' . $id,
            'nama_mk'   => 'sometimes|string|max:100',
            'sks'       => 'sometimes|integer|min:1',
            'prodi_id'  => 'sometimes|exists:prodi,id',
        ]);

        $mataKuliah->update($validated);

        return response()->json($mataKuliah);
    }

    /**
     * Hapus mata kuliah
     */
    public function destroy(int $id): JsonResponse
    {
        $mataKuliah = MataKuliah::findOrFail($id);
        $mataKuliah->delete();

        return response()->json(null, 204);
    }

}
