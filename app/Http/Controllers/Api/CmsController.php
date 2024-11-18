<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CmsController extends Controller
{
    /**
     * Menampilkan daftar CMS.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get pagination parameters (start and length) from the request
        $start = $request->input('start', 0);   // Default start is 0
        $length = $request->input('length', 10); // Default length is 10
        $search = $request->input('search.value', ''); // Search query, if available

        // Base query for Cms with join to terminals table for terminal_code
        $baseQuery = Cms::query()
            ->join('terminals', 'cms.terminal_id', '=', 'terminals.terminal_id')
            ->select(
                'cms.terminal_id',
                'cms.cms_code',
                'cms.cms_name',
                'cms.cms_value',
                'terminals.terminal_code'
            );

        // Apply search filter if there’s a search value
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(cms.cms_code)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(cms.cms_name)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(cms.cms_value)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(terminals.terminal_code)'), 'LIKE', "%" . strtolower($search) . "%");
            });
        }

        // Clone the base query to count filtered records
        $recordsFiltered = $baseQuery->count();

        // Apply pagination to the query
        $data = $baseQuery->offset($start)->limit($length)->get();

        // Count total records without filter for recordsTotal
        $recordsTotal = Cms::count();

        // Return data in the desired format for DataTables
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diambil',
            'recordsTotal' => $recordsTotal,       // Total records in the database
            'recordsFiltered' => $recordsFiltered, // Total records after filtering
            'data' => $data                        // Data to be displayed on the page
        ], 200);
    }

    public function getByTerminalCode($terminal_code)
    {
        // Base query for Cms with join to terminals table for terminal_code
        $baseQuery = Cms::query()
            ->join('terminals', 'cms.terminal_id', '=', 'terminals.terminal_id')
            ->select(
                'cms.terminal_id',
                'cms.cms_code',
                'cms.cms_name',
                'cms.cms_value',
                'terminals.terminal_code'
            )
            ->where('terminals.terminal_code', $terminal_code)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data CMS berhasil diambil',
            'data' => $baseQuery
        ], 200);

    }

    // public function index()
    // {
    //     // Mengambil semua data CMS
    //     $cms = Cms::all();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data CMS berhasil diambil',
    //         'data' => $cms
    //     ], 200);
    // }

    /**
     * Mengupdate nilai CMS berdasarkan ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function edit($id)
    {
        // Cari CMS berdasarkan ID
        $cms = Cms::find($id);

        if (!$cms) {
            return response()->json([
                'status' => 'error',
                'message' => 'CMS tidak ditemukan',
            ], 404);
        }

        // Return CMS data
        return response()->json([
            'status' => 'success',
            'message' => 'Data CMS berhasil diambil',
            'data' => $cms,
        ], 200);
    }

    // public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'cms_value' => 'required|string|max:225',
    //     ]);

    //     // Cari CMS berdasarkan cms_id (adjust if cms_id is not the primary key)
    //     $cms = Cms::where('cms_id', $id)->first();

    //     if (!$cms) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'CMS tidak ditemukan',
    //         ], 404);
    //     }

    //     // Update hanya cms_value
    //     $cms->cms_value = $request->input('cms_value');
    //     $cms->updated_by = Auth::check() ? Auth::id() : 1; // Set ID user yang mengedit
    //     $cms->updated_at = now();
    //     $cms->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'CMS berhasil diperbarui',
    //         'data' => $cms,
    //     ], 200);
    // }

    public function update($cms_code, Request $request)
    {
        $cms = Cms::where('cms_code', $cms_code)->first();

        if (!$cms) {
            return response()->json(['status' => 'error', 'message' => 'CMS tidak ditemukan'], 404);
        }

        $cms->update([
            'cms_value' => $request->input('cms_value'),
        ]);

        return response()->json(['status' => 'success', 'message' => 'CMS berhasil diperbarui', 'data' => $cms]);
    }
    
    // public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $request->validate([
    //         'cms_value' => 'required|string|max:225', // hanya kolom value yang bisa diubah
    //     ]);

    //     // Cari CMS berdasarkan ID
    //     // $cms = Cms::find($id);
    //     $cms = Cms::where('cms_id', $id)->first();

    //     if (!$cms) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'CMS tidak ditemukan',
    //         ], 404);
    //     }

    //     // Update hanya cms_value
    //     $cms->cms_value = $request->input('cms_value');
    //     $cms->updated_by = Auth::check() ? Auth::id() : 1; // Set ID user yang mengedit
    //     $cms->updated_at = now();
    //     $cms->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'CMS berhasil diperbarui',
    //         'data' => $cms,
    //     ], 200);
    // }
}
