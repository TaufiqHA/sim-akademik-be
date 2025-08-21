<?php

namespace App\Http\Controllers;

use App\Models\KRS;
use App\Models\KRSDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KRSController extends Controller
{
    // GET /krs
    public function index(Request $request)
    {
        $query = KRS::query();

        if ($request->has('mahasiswa_id')) {
            $query->where('mahasiswa_id', $request->mahasiswa_id);
        }
        if ($request->has('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate($request->get('per_page', 15)));
    }

    // POST /krs
    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|integer|exists:users,id',
            'tahun_akademik_id' => 'required|integer|exists:tahun_akademiks,id',
        ]);

        $krs = KRS::create([
            'mahasiswa_id' => $request->mahasiswa_id,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'status' => 'Draft',
        ]);

        return response()->json($krs, 201);
    }

    // GET /krs/{id}
    public function show($id)
    {
        $krs = KRS::with('details')->findOrFail($id);
        return response()->json($krs);
    }

    // DELETE /krs/{id}
    public function destroy($id)
    {
        $krs = KRS::findOrFail($id);

        if ($krs->status !== 'Draft') {
            return response()->json(['message' => 'Hanya Draft yang bisa dihapus'], 409);
        }

        $krs->delete();
        return response()->json(null, 204);
    }

    // POST /krs/{id}/submit
    public function submit($id)
    {
        $krs = KRS::findOrFail($id);

        if ($krs->status !== 'Draft') {
            return response()->json(['message' => 'KRS sudah disubmit/approved'], 409);
        }

        $krs->status = 'Submitted';
        $krs->save();

        return response()->json(['message' => 'KRS berhasil disubmit']);
    }

    // POST /krs/{id}/approve
    public function approve($id)
    {
        $krs = KRS::findOrFail($id);

        if ($krs->status !== 'Submitted') {
            return response()->json(['message' => 'KRS tidak bisa di-approve'], 409);
        }

        $krs->status = 'Approved';
        $krs->save();

        return response()->json(['message' => 'KRS berhasil diapprove']);
    }

    // GET /krs/{id}/detail
    public function details($id)
    {
        $details = KRSDetail::where('krs_id', $id)->get();
        return response()->json($details);
    }

    // POST /krs/{id}/detail
    public function addDetail(Request $request, $id)
    {
        $request->validate([
            'jadwal_kuliah_id' => 'required|integer|exists:jadwal_kuliahs,id'
        ]);

        $krs = KRS::findOrFail($id);
        if ($krs->status !== 'Draft') {
            return response()->json(['message' => 'Tidak bisa menambah detail, KRS bukan Draft'], 409);
        }

        $detail = KRSDetail::create([
            'krs_id' => $krs->id,
            'jadwal_kuliah_id' => $request->jadwal_kuliah_id
        ]);

        return response()->json($detail, 201);
    }

    // DELETE /krs/{id}/detail/{detailId}
    public function removeDetail($id, $detailId)
    {
        $krs = KRS::findOrFail($id);
        $detail = KRSDetail::where('krs_id', $id)->where('id', $detailId)->firstOrFail();

        if ($krs->status !== 'Draft') {
            return response()->json(['message' => 'Tidak bisa hapus detail, KRS bukan Draft'], 409);
        }

        $detail->delete();
        return response()->json(null, 204);
    }

    public function getByJadwal(Request $request)
    {
        $jadwalId = $request->query('jadwal_kuliah_id');

        if (!$jadwalId) {
            return response()->json([
                'message' => 'jadwal_kuliah_id wajib diisi'
            ], 400);
        }

        $data = DB::table('k_r_s')
            ->join('k_r_s_detail', 'k_r_s_detail.krs_id', '=', 'k_r_s.id')
            ->join('users', 'users.id', '=', 'k_r_s.mahasiswa_id')
            ->join('mahasiswa_profiles', 'mahasiswa_profiles.user_id', '=', 'users.id')
            ->select(
                'k_r_s.id',
                'k_r_s.mahasiswa_id',
                'k_r_s_detail.jadwal_kuliah_id',
                'k_r_s.status',
                'users.id as mhs_user_id',
                'users.nama',
                'mahasiswa_profiles.nim'
            )
            ->where('k_r_s_detail.jadwal_kuliah_id', $jadwalId)
            ->where('k_r_s.status', 'Approved')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'mahasiswa_id' => $row->mahasiswa_id,
                    'jadwal_kuliah_id' => $row->jadwal_kuliah_id,
                    'status' => $row->status,
                    'mahasiswa' => [
                        'id' => $row->mhs_user_id,
                        'nama' => $row->nama,
                        'nim' => $row->nim
                    ]
                ];
            });

        return response()->json($data);
    }


}
