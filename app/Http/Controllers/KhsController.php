<?php

namespace App\Http\Controllers;

use App\Models\Khs;
use App\Models\Nilai;
use App\Models\KhsDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class KhsController extends Controller
{
    /**
     * GET /khs → list KHS
     */
    public function index(Request $request)
    {
        $query = Khs::query();

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }

        if ($request->has('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        return response()->json($query->paginate($request->per_page ?? 15));
    }

    /**
     * GET /khs/{id} → detail KHS
     */
    public function show($id)
    {
        $khs = Khs::with('details.mataKuliah')->findOrFail($id);
        return response()->json($khs);
    }

    /**
     * GET /khs/{id}/detail → daftar mata kuliah dalam KHS
     */
    public function detail($id)
    {
        $details = KhsDetail::where('khs_id', $id)
            ->with('mataKuliah')
            ->get();

        return response()->json($details);
    }

    /**
     * GET /khs/{id}/download → export PDF
     */
    public function download($id)
    {
        $khs = Khs::with('details.mataKuliah')->findOrFail($id);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.khs', compact('khs'));

        return $pdf->download("KHS-{$khs->id}.pdf");
    }

    /**
     * Tambahan: generate KHS dari nilai per semester
     */
    public function generate(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|integer|exists:users,id',
            'tahun_akademik_id' => 'required|integer|exists:tahun_akademiks,id',
        ]);

        $mahasiswaId = $request->mahasiswa_id;
        $taId = $request->tahun_akademik_id;

        $nilaiList = Nilai::where('mahasiswa_id', $mahasiswaId)
            ->whereHas('jadwalKuliah', function ($q) use ($taId) {
                $q->where('tahun_akademik_id', $taId);
            })
            ->with('jadwalKuliah.mataKuliah')
            ->get();

        if ($nilaiList->isEmpty()) {
            return response()->json(['message' => 'Belum ada nilai untuk semester ini'], 404);
        }

        $totalBobot = 0;
        $totalSks = 0;

        $khs = Khs::updateOrCreate(
            ['mahasiswa_id' => $mahasiswaId, 'tahun_akademik_id' => $taId],
            ['ip_semester' => 0, 'sks_semester' => 0]
        );

        $khs->details()->delete();

        foreach ($nilaiList as $nilai) {
            $mk = $nilai->jadwalKuliah->mataKuliah;
            $huruf = $this->konversiHuruf($nilai->nilai_akhir);
            $bobot = $this->bobotNilai($huruf);

            $totalBobot += $bobot * $mk->sks;
            $totalSks += $mk->sks;

            KhsDetail::create([
                'khs_id' => $khs->id,
                'mata_kuliah_id' => $mk->id,
                'nilai_huruf' => $huruf,
                'nilai_angka' => $nilai->nilai_akhir,
            ]);
        }

        $ipSemester = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        $khs->update([
            'ip_semester' => $ipSemester,
            'sks_semester' => $totalSks,
        ]);

        return response()->json([
            'message' => 'KHS berhasil digenerate',
            'data' => $khs->load('details.mataKuliah')
        ]);
    }

    /**
     * Tambahan: GET /khs/ipk/{mahasiswa_id} → hitung IPK kumulatif
     */
    public function ipk($mahasiswa_id)
    {
        $allKhs = Khs::where('mahasiswa_id', $mahasiswa_id)
            ->with('details.mataKuliah')
            ->get();

        $totalBobot = 0;
        $totalSks = 0;

        foreach ($allKhs as $khs) {
            foreach ($khs->details as $detail) {
                $bobot = $this->bobotNilai($detail->nilai_huruf);
                $totalBobot += $bobot * $detail->mataKuliah->sks;
                $totalSks += $detail->mataKuliah->sks;
            }
        }

        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        return response()->json([
            'mahasiswa_id' => $mahasiswa_id,
            'total_sks' => $totalSks,
            'ipk' => $ipk
        ]);
    }

    private function konversiHuruf($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 70) return 'B';
        if ($nilai >= 55) return 'C';
        if ($nilai >= 40) return 'D';
        return 'E';
    }

    private function bobotNilai($huruf)
    {
        return match ($huruf) {
            'A' => 4,
            'B' => 3,
            'C' => 2,
            'D' => 1,
            default => 0,
        };
    }

}
