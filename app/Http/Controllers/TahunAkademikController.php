<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAkademik;
use Illuminate\Http\JsonResponse;

class TahunAkademikController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TahunAkademik::query();

        if ($request->has('is_aktif')) {
            $query->where('is_aktif', $request->is_aktif);
        }

        $perPage = $request->get('per_page', 15);
        $tahunAkademik = $query->paginate($perPage);

        return response()->json($tahunAkademik);
    }

    /**
     * Simpan tahun akademik baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'is_aktif' => 'boolean',
            'periode_krs_mulai' => 'nullable|date',
            'periode_krs_selesai' => 'nullable|date',
            'periode_nilai_mulai' => 'nullable|date',
            'periode_nilai_selesai' => 'nullable|date',
        ]);

        $tahunAkademik = TahunAkademik::create($validated);

        return response()->json($tahunAkademik, 201);
    }

    /**
     * Detail tahun akademik
     */
    public function show(int $id): JsonResponse
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);
        return response()->json($tahunAkademik);
    }

    /**
     * Update tahun akademik
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'tahun' => 'sometimes|string|max:20',
            'semester' => 'sometimes|in:Ganjil,Genap',
            'is_aktif' => 'sometimes|boolean',
            'periode_krs_mulai' => 'nullable|date',
            'periode_krs_selesai' => 'nullable|date',
            'periode_nilai_mulai' => 'nullable|date',
            'periode_nilai_selesai' => 'nullable|date',
        ]);

        $tahunAkademik = TahunAkademik::findOrFail($id);
        $tahunAkademik->update($validated);

        return response()->json($tahunAkademik);
    }

    /**
     * Hapus tahun akademik
     */
    public function destroy(int $id): JsonResponse
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);
        $tahunAkademik->delete();

        return response()->json(null, 204);
    }

    /**
     * Set tahun akademik aktif (nonaktifkan yang lain)
     */
    public function setAktif(int $id): JsonResponse
    {
        // Nonaktifkan semua
        TahunAkademik::query()->update(['is_aktif' => false]);

        // Aktifkan hanya yang dipilih
        $tahunAkademik = TahunAkademik::findOrFail($id);
        $tahunAkademik->update(['is_aktif' => true]);

        return response()->json([
            'message' => 'Tahun akademik berhasil diaktifkan',
            'data' => $tahunAkademik
        ]);
    }

}
