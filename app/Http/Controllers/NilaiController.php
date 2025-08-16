<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    // GET /nilai → list nilai (filter mahasiswa/jadwal)
    public function index(Request $request)
    {
        $query = Nilai::query();

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        if ($request->has('jadwal_kuliah_id')) {
            $query->where('jadwal_kuliah_id', $request->jadwal_kuliah_id);
        }

        return response()->json($query->paginate($request->per_page ?? 15));
    }

    // GET /nilai/{id} → detail nilai
    public function show($id)
    {
        $nilai = Nilai::findOrFail($id);
        return response()->json($nilai);
    }

    // POST /nilai → input/update nilai oleh dosen
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id'    => 'required|integer|exists:users,id',
            'jadwal_kuliah_id'=> 'required|integer|exists:jadwal_kuliahs,id',
            'tugas'           => 'nullable|numeric|min:0|max:100',
            'uts'             => 'nullable|numeric|min:0|max:100',
            'uas'             => 'nullable|numeric|min:0|max:100',
        ]);

        $nilaiAkhir = (
            ($request->tugas ?? 0) * 0.3 +
            ($request->uts ?? 0) * 0.3 +
            ($request->uas ?? 0) * 0.4
        );

        // update jika sudah ada, kalau belum buat
        $nilai = Nilai::updateOrCreate(
            [
                'mahasiswa_id' => $request->mahasiswa_id,
                'jadwal_kuliah_id' => $request->jadwal_kuliah_id,
            ],
            [
                'tugas' => $request->tugas,
                'uts' => $request->uts,
                'uas' => $request->uas,
                'nilai_akhir' => $nilaiAkhir,
            ]
        );

        return response()->json([
            'message' => 'Nilai berhasil disimpan',
            'data' => $nilai
        ]);
    }

    // DELETE /nilai/{id} → hapus nilai
    public function destroy($id)
    {
        $nilai = Nilai::findOrFail($id);
        $nilai->delete();

        return response()->json(['message' => 'Nilai berhasil dihapus']);
    }

    // POST /nilai/{id}/finalize → kunci nilai akhir
    public function finalize($id)
    {
        $nilai = Nilai::findOrFail($id);

        // TODO: validasi periode_nilai (tahun_akademik)
        // untuk sekarang langsung finalize
        $nilai->update([
            'nilai_akhir' => $nilai->nilai_akhir, // sudah dihitung di store
        ]);

        return response()->json(['message' => 'Nilai berhasil difinalisasi']);
    }

}
