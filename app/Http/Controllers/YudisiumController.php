<?php

namespace App\Http\Controllers;

use App\Models\Yudisium;
use Illuminate\Http\Request;

class YudisiumController extends Controller
{
    /**
     * List data yudisium
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $yudisium = Yudisium::with(['mahasiswa', 'pembimbing', 'penguji'])
            ->paginate($perPage);

        return response()->json($yudisium);
    }

    /**
     * Simpan data yudisium baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id'  => 'required|exists:users,id',
            'ipk'           => 'required|numeric|min:0|max:4',
            'nilai_sidang'  => 'nullable|numeric|min:0|max:100',
            'pembimbing_id' => 'nullable|exists:users,id',
            'penguji_id'    => 'nullable|exists:users,id',
        ]);

        $yudisium = Yudisium::create($validated);

        return response()->json($yudisium, 201);
    }

    /**
     * Approve yudisium
     */
    public function approve($id)
    {
        $yudisium = Yudisium::findOrFail($id);
        $yudisium->status = 'Approved';
        $yudisium->alasan_reject = null;
        $yudisium->save();

        return response()->json(['message' => 'Approved']);
    }

    /**
     * Reject yudisium
     */
    public function reject(Request $request, $id)
    {
        $yudisium = Yudisium::findOrFail($id);

        $yudisium->status = 'Rejected';
        $yudisium->alasan_reject = $request->input('alasan', null);
        $yudisium->save();

        return response()->json(['message' => 'Rejected']);
    }

    /**
     * Cek kelayakan mahasiswa lulus
     */
    public function checkEligibility($id)
    {
        $yudisium = Yudisium::with('mahasiswa')->findOrFail($id);

        $isEligible = true;
        $reason = 'Lolos persyaratan';

        if ($yudisium->ipk < 2.00) {
            $isEligible = false;
            $reason = 'IPK kurang dari 2.00';
        } elseif (is_null($yudisium->nilai_sidang) || $yudisium->nilai_sidang < 60) {
            $isEligible = false;
            $reason = 'Nilai sidang tidak memenuhi';
        }

        return response()->json([
            'is_eligible' => $isEligible,
            'reason'      => $reason,
        ]);
    }
}
