<?php

namespace App\Http\Controllers;

use App\Models\KRS;
use App\Models\KRSDetail;
use Illuminate\Http\Request;

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

}
