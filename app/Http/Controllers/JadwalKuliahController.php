<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JadwalKuliahController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JadwalKuliah::with(['mataKuliah', 'dosen', 'tahunAkademik']);

        if ($request->has('prodi_id')) {
            $query->whereHas('mataKuliah', function ($q) use ($request) {
                $q->where('prodi_id', $request->prodi_id);
            });
        }

        if ($request->has('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->has('dosen_id')) {
            $query->where('dosen_id', $request->dosen_id);
        }

        $perPage = $request->get('per_page', 15);
        $jadwal = $query->paginate($perPage);

        return response()->json($jadwal);
    }

    /**
     * Simpan jadwal kuliah baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mata_kuliah_id'     => 'required|exists:mata_kuliahs,id',
            'dosen_id'           => 'required|exists:users,id',
            'ruang'              => 'required|string|max:50',
            'hari'               => 'required|string|max:20',
            'jam_mulai'          => 'required|date_format:H:i',
            'jam_selesai'        => 'required|date_format:H:i|after:jam_mulai',
            'tahun_akademik_id'  => 'required|exists:tahun_akademiks,id',
        ]);

        $jadwal = JadwalKuliah::create($validated);

        return response()->json($jadwal, 201);
    }

    /**
     * Detail jadwal kuliah
     */
    public function show(int $id): JsonResponse
    {
        $jadwal = JadwalKuliah::with(['mataKuliah', 'dosen', 'tahunAkademik'])->findOrFail($id);
        return response()->json($jadwal);
    }

    /**
     * Update jadwal kuliah
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'mata_kuliah_id'     => 'sometimes|exists:mata_kuliahs,id',
            'dosen_id'           => 'sometimes|exists:users,id',
            'ruang'              => 'sometimes|string|max:50',
            'hari'               => 'sometimes|string|max:20',
            'jam_mulai'          => 'sometimes|date_format:H:i',
            'jam_selesai'        => 'sometimes|date_format:H:i|after:jam_mulai',
            'tahun_akademik_id'  => 'sometimes|exists:tahun_akademiks,id',
        ]);

        $jadwal = JadwalKuliah::findOrFail($id);
        $jadwal->update($validated);

        return response()->json($jadwal);
    }

    /**
     * Hapus jadwal kuliah
     */
    public function destroy(int $id): JsonResponse
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $jadwal->delete();

        return response()->json(null, 204);
    }

}
