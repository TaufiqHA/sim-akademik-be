<?php

namespace App\Http\Controllers;

use App\Models\MateriKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MateriKuliahController extends Controller
{
    /**
     * GET /materi-kuliah → list materi kuliah
     */
    public function index(Request $request)
    {
        $query = MateriKuliah::query()->with('jadwalKuliah.mataKuliah');

        if ($request->has('jadwal_kuliah_id')) {
            $query->where('jadwal_kuliah_id', $request->jadwal_kuliah_id);
        }

        return response()->json($query->paginate($request->per_page ?? 15));
    }

    /**
     * GET /materi-kuliah/{id} → detail materi
     */
    public function show($id)
    {
        $materi = MateriKuliah::with('jadwalKuliah.mataKuliah')->findOrFail($id);
        return response()->json($materi);
    }

    /**
     * POST /materi-kuliah → upload materi
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliahs,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:10240'
        ]);

        $path = $request->file('file')->store('materi', 'public');

        $materi = MateriKuliah::create([
            'jadwal_kuliah_id' => $request->jadwal_kuliah_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $path,
            'uploaded_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Materi berhasil diupload',
            'data' => $materi
        ], 201);
    }

    /**
     * PUT /materi-kuliah/{id} → update materi
     */
    public function update(Request $request, $id)
    {
        $materi = MateriKuliah::findOrFail($id);

        if(!$materi)
        {
            return response()->json(['message' => 'Materi tidak ditemukan'], 404);
        }

        // $validated = $request->validate([
        //     'judul' => 'sometimes|required|string|max:255',
        //     'deskripsi' => 'nullable|string',
        //     'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,xlsx,xls|max:10240'
        // ]);

        $validator = Validator::make($request->all(), [
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,xlsx,xls|max:10240'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('materi', 'public');
        }

        $materi->update($validated);
        $materi->refresh();

        return response()->json([
            'message' => 'Materi berhasil diperbarui',
            'data' => $materi
        ]);
    }

    /**
     * DELETE /materi-kuliah/{id} → hapus materi
     */
    public function destroy($id)
    {
        $materi = MateriKuliah::findOrFail($id);

        if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return response()->json(['message' => 'Materi berhasil dihapus']);
    }

}
