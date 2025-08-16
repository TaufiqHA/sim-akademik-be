<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JurusanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Jurusan::query();

        if ($request->has('prodi_id')) {
            $query->where('prodi_id', $request->prodi_id);
        }

        $perPage = $request->get('per_page', 15);
        $jurusan = $query->paginate($perPage);

        return response()->json($jurusan);
    }

    /**
     * Simpan jurusan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'prodi_id' => 'required|exists:prodis,id',
            'nama_jurusan' => 'required|string|max:100'
        ]);

        $jurusan = Jurusan::create($validated);

        return response()->json($jurusan, 201);
    }

    /**
     * Detail jurusan.
     */
    public function show(int $id): JsonResponse
    {
        $jurusan = Jurusan::findOrFail($id);
        return response()->json($jurusan);
    }

    /**
     * Update jurusan.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'prodi_id' => 'sometimes|exists:prodis,id',
            'nama_jurusan' => 'sometimes|string|max:100'
        ]);

        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update($validated);

        return response()->json($jurusan);
    }

    /**
     * Hapus jurusan.
     */
    public function destroy(int $id): JsonResponse
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();

        return response()->json(null, 204);
    }

}
