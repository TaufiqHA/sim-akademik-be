<?php

namespace App\Http\Controllers;

use App\Models\JadwalUjian;
use Illuminate\Http\Request;

class JadwalUjianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'required|exists:users,id',
            'ruang' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'jenis_ujian' => 'required|in:UTS,UAS',
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id'
        ]);

        $jadwal = JadwalUjian::create($request->all());

        return response()->json($jadwal->load(['mataKuliah', 'dosen']), 201);
    }

    /**
     * Update jadwal ujian
     */
    public function update(Request $request, $id)
    {
        $jadwal = JadwalUjian::findOrFail($id);

        $request->validate([
            'mata_kuliah_id' => 'sometimes|exists:mata_kuliahs,id',
            'dosen_id' => 'sometimes|exists:users,id',
            'ruang' => 'sometimes|string|max:100',
            'tanggal' => 'sometimes|date',
            'jam_mulai' => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i|after:jam_mulai',
            'jenis_ujian' => 'sometimes|in:UTS,UAS',
            'tahun_akademik_id' => 'sometimes|exists:tahun_akademiks,id'
        ]);

        $jadwal->update($request->all());

        return response()->json($jadwal->load(['mataKuliah', 'dosen']));
    }

    /**
     * Hapus jadwal ujian
     */
    public function destroy($id)
    {
        $jadwal = JadwalUjian::findOrFail($id);
        $jadwal->delete();

        return response()->json(['message' => 'Jadwal ujian berhasil dihapus'], 204);
    }

}
