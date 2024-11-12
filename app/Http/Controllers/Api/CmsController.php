<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CmsController extends Controller
{
    /**
     * Menampilkan daftar CMS.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data CMS
        $cms = Cms::all();

        return response()->json([
            'status' => true,
            'message' => 'Data CMS berhasil diambil',
            'data' => $cms
        ], 200);
    }

    /**
     * Mengupdate nilai CMS berdasarkan ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'cms_value' => 'required|string|max:225', // hanya kolom value yang bisa diubah
        ]);

        // Cari CMS berdasarkan ID
        $cms = Cms::find($id);

        if (!$cms) {
            return response()->json([
                'status' => 'error',
                'message' => 'CMS tidak ditemukan',
            ], 404);
        }

        // Update hanya cms_value
        $cms->cms_value = $request->input('cms_value');
        $cms->updated_by = Auth::check() ? Auth::id() : 1; // Set ID user yang mengedit
        $cms->updated_at = now();
        $cms->save();

        return response()->json([
            'status' => 'success',
            'message' => 'CMS berhasil diperbarui',
            'data' => $cms,
        ], 200);
    }
}
