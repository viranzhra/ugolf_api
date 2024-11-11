<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{
    /**
     * Menampilkan daftar semua merchant.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data merchant
        $merchants = Merchant::all();

        return response()->json([
            'status' => true,
            'message' => 'Data merchant berhasil diambil',
            'data' => $merchants
        ], 200);
    }

    /**
     * Menambahkan merchant baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'merchant_code' => 'required|string|max:100|unique:merchants',  // Unik untuk setiap merchant
            'merchant_name' => 'required|string|max:100',
            'merchant_address' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        // Menyimpan merchant baru
        $merchant = new Merchant();
        $merchant->merchant_code = $request->input('merchant_code');
        $merchant->merchant_name = $request->input('merchant_name');
        $merchant->merchant_address = $request->input('merchant_address');
        $merchant->description = $request->input('description');
        $merchant->created_by = Auth::id(); // ID user yang menambahkan
        $merchant->created_at = now(); // Waktu pembuatan
        $merchant->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Merchant berhasil ditambahkan',
            'data' => $merchant
        ], 201);
    }

    /**
     * Mengupdate data merchant berdasarkan ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'merchant_code' => 'required|string|max:100|unique:merchants',  // Unik untuk setiap merchant
        'merchant_name' => 'required|string|max:100',
        'merchant_address' => 'required|string',
        'description' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => $validator->errors()
        ], 400);
    }

    // Cari merchant berdasarkan ID
    $merchant = Merchant::find($id);

    if (!$merchant) {
        return response()->json([
            'status' => 'error',
            'message' => 'Merchant tidak ditemukan'
        ], 404);
    }

    // Update data merchant
    $merchant->merchant_code = $request->input('merchant_code');
    $merchant->merchant_name = $request->input('merchant_name');
    $merchant->merchant_address = $request->input('merchant_address');
    $merchant->description = $request->input('description');
    
    // Set updated_by manually (you can replace it with a fixed value or simulate a user ID)
    // $merchant->updated_by = 1;  // Set manually, e.g., 1 (for testing or debugging)

    $merchant->updated_at = now(); // Update the timestamp for the update action
    
    // Simulate saving the updated merchant
    $merchant->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Merchant berhasil diperbarui',
        'data' => $merchant
    ], 200);
}

    /**
     * Menghapus merchant berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Cari merchant berdasarkan ID
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant tidak ditemukan'
            ], 404);
        }

        // Menghapus merchant
        $merchant->deleted_by = Auth::id(); // ID user yang menghapus
        $merchant->deleted_at = now(); // Waktu penghapusan
        $merchant->save();
        $merchant->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Merchant berhasil dihapus'
        ], 200);
    }
}
