<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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
            'message' => 'Data CMS tidak ditemukan',
            'data' => $cms
        ], 200);
    }

    public function store(Request $request)
    {
        // Validasi input untuk cms_name dan cms_value
        $validator = Validator::make($request->all(), [
            'cms_name' => 'required|string|max:100',  // Validasi untuk nama CMS
            'cms_value' => 'required|string|max:225', // Validasi untuk value CMS
            'terminal_id' => 'required|integer',      // Validasi untuk terminal_id
            'cms_code' => 'required|integer',         // Validasi untuk cms_code
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Menyimpan data CMS baru
        $cms = new Cms();
        $cms->cms_name = $request->input('cms_name');
        $cms->cms_value = $request->input('cms_value');
        $cms->terminal_id = $request->input('terminal_id');
        $cms->cms_code = $request->input('cms_code');
        $cms->created_by = Auth::id(); // ID user yang menambahkan
        $cms->created_at = now(); // Waktu pembuatan
        $cms->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data CMS berhasil ditambahkan',
            'data' => $cms
        ], 201);
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
        // Validasi input untuk cms_value
        $validator = Validator::make($request->all(), [
            'cms_value' => 'required|string|max:225', // Validasi untuk kolom value
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Cari data CMS berdasarkan ID
        $cms = Cms::find($id);

        if (!$cms) {
            return response()->json([
                'status' => 'error',
                'message' => 'CMS tidak ditemukan'
            ], 404);
        }

        // Update kolom cms_value dan informasi update lainnya
        $cms->cms_value = $request->input('cms_value');
        $cms->updated_by = Auth::id(); // ID user yang mengupdate
        $cms->updated_at = now(); // Waktu update
        $cms->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Nilai CMS berhasil diperbarui',
            'data' => $cms
        ], 200);
    }
}
