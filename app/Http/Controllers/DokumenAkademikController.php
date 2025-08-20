<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DokumenAkademik;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DokumenAkademikController extends Controller
{
    public function index(Request $request)
    {
        $query = DokumenAkademik::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('jenis_dokumen')) {
            $query->where('jenis_dokumen', 'like', '%'.$request->jenis_dokumen.'%');
        }

        return response()->json($query->paginate($request->get('per_page', 10)));
    }

    // Upload dokumen
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_dokumen' => 'required|string|max:100',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:5120',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $path = $request->file('file')->store('dokumen-akademik', 'public');

        $dokumen = DokumenAkademik::create([
            'jenis_dokumen' => $request->jenis_dokumen,
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
            'status' => 'Pending',
        ]);

        return response()->json($dokumen, 201);
    }

    // Detail
    public function show($id)
    {
        $dokumen = DokumenAkademik::findOrFail($id);
        return response()->json($dokumen);
    }

    // Approve dokumen
    public function approve($id)
    {
        $dokumen = DokumenAkademik::findOrFail($id);
        $dokumen->update([
            'status' => 'Approved',
            'approved_by' => Auth::id()
        ]);

        return response()->json(['message' => 'Dokumen disetujui']);
    }

    // Reject dokumen
    public function reject(Request $request, $id)
    {
        $dokumen = DokumenAkademik::findOrFail($id);
        $dokumen->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Dokumen ditolak',
            'alasan' => $request->alasan ?? null
        ]);
    }

    // Hapus
    public function destroy($id)
    {
        $dokumen = DokumenAkademik::findOrFail($id);

        if ($dokumen->file_path) {
            Storage::disk('public')->delete($dokumen->file_path);
        }

        $dokumen->delete();

        return response()->json(['message' => 'Dokumen dihapus']);
    }

}
