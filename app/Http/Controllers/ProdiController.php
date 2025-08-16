<?php

namespace App\Http\Controllers;

use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProdiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Prodi::query();

        if ($request->has('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }

        $perPage = $request->get('per_page', 15);
        $prodi = $query->paginate($perPage);

        return response()->json($prodi);
    }

    /**
     * Simpan prodi baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'nama_prodi' => 'required|string|max:100'
        ]);

        $prodi = Prodi::create($validated);

        return response()->json($prodi, 201);
    }

    /**
     * Detail prodi.
     */
    public function show(int $id): JsonResponse
    {
        $prodi = Prodi::findOrFail($id);
        return response()->json($prodi);
    }

    /**
     * Update prodi.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'fakultas_id' => 'sometimes|exists:fakultas,id',
            'nama_prodi' => 'sometimes|string|max:100'
        ]);

        $prodi = Prodi::findOrFail($id);
        $prodi->update($validated);

        return response()->json($prodi);
    }

    /**
     * Hapus prodi.
     */
    public function destroy(int $id): JsonResponse
    {
        $prodi = Prodi::findOrFail($id);
        $prodi->delete();

        return response()->json(null, 204);
    }

}
