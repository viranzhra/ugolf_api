<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
// use Yajra\DataTables\Facades\DataTables;

class MerchantController extends Controller
{

    // public function index()
    // {
    //     $merchants = Merchant::all();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Data merchant berhasil diambil',
    //         'data' => $merchants
    //     ], 200);
    // }

    public function index(Request $request)
    {
        // Ambil parameter pagination start dan length dari request
        $start = $request->input('start', 0);   // Default start adalah 0
        $length = $request->input('length', 10); // Default length adalah 10
        $search = $request->input('search.value', ''); // Jika ada pencarian

        // Query merchant yang sudah difilter berdasarkan pencarian jika ada
        // $baseQuery = Merchant::query();
        // Prepare base query
        $baseQuery = DB::table('merchants')
            // ->leftJoin('merchants', 'merchants.merchant_id', '=', 'merchants.merchant_id')
            ->select(
                'merchants.*',
                'merchants.merchant_code',
            );

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(merchants.merchant_code)'), 'LIKE', "%" . strtolower($search) . "%")
                    // ->orWhere(DB::raw('LOWER(merchants.merchant_code)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(merchants.merchant_name)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(merchants.merchant_address)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(merchants.description)'), 'LIKE', "%" . strtolower($search) . "%");
            });
        }

        // Clone the base query to count filtered records
        $recordsFiltered = $baseQuery->count();

        // Apply pagination to the query
        $merchants = $baseQuery->offset($start)->limit($length)->get();

        // Hitung total data tanpa filter untuk recordsTotal
        $recordsTotal = Merchant::count(); // Ini adalah jumlah data total di database tanpa filter apapun

        // Mengembalikan data dalam format yang diinginkan DataTables
        return response()->json([
            'status' => true,
            'message' => 'Data merchant berhasil diambil',
            'recordsTotal' => $recordsTotal, // Total jumlah data di database
            'recordsFiltered' => $recordsFiltered, // Total data setelah filter
            'data' => $merchants // Data yang akan ditampilkan di halaman
        ], 200);
    }

    // Method to generate merchant_code
    // public function getMerchantCode()
    // {
    //     $faker = Faker::create();

    //     // Generate merchant_code using Faker
    //     $merchant_code = $faker->unique()->numerify('MC-####');

    //     // Return the merchant_code as a response
    //     return response()->json([
    //         'status' => 'success',
    //         'merchant_code' => $merchant_code,
    //     ]);
    // }

    //     public function store(Request $request)
    // {
    //     // Validasi input
    //     $validator = Validator::make($request->all(), [
    //         // 'merchant_code' => 'required|string|max:100|unique:merchants', // Pastikan merchant_code unik
    //         'merchant_name' => 'required|string|max:100',
    //         'merchant_address' => 'required|string',
    //         'description' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors()
    //         ], 400);
    //     }

    //     // Ambil merchant_code dari input pengguna
    //     // $merchant_code_input = $request->input('merchant_code');

    //     // Membuat merchant baru
    //     $merchant = new Merchant();
    //     // $merchant->merchant_code = $merchant_code_input; // Simpan merchant_code yang diinputkan pengguna
    //     $merchant->merchant_name = $request->input('merchant_name');
    //     $merchant->merchant_address = $request->input('merchant_address');
    //     $merchant->description = $request->input('description');
    //     $merchant->created_by = Auth::check() ? Auth::id() : 1; // Default ke ID 1 jika tidak terautentikasi
    //     $merchant->created_at = now();
    //     $merchant->save();

    //     // Kembalikan respons sukses
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Merchant berhasil ditambahkan',
    //         'data' => $merchant
    //     ], 201);
    // }


    public function store(Request $request)
    {
        // Initialize Faker
        $faker = Faker::create();

        // Generate a unique merchant_code
        $merchant_code = $faker->unique()->numerify('MC-####');

        // Validate other input fields
        $validator = Validator::make($request->all(), [
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

        // Create a new merchant with generated merchant_code and other inputs
        $merchant = new Merchant();
        $merchant->merchant_code = $merchant_code; // Generated code
        $merchant->merchant_name = $request->input('merchant_name');
        $merchant->merchant_address = $request->input('merchant_address');
        $merchant->description = $request->input('description');
        $merchant->created_by = Auth::check() ? Auth::id() : 1; // Default to ID 1 if not authenticated
        $merchant->created_at = now();
        $merchant->save();

        // Return a success response with the created merchant data
        return response()->json([
            'status' => 'success',
            'message' => 'Merchant berhasil ditambahkan',
            'data' => $merchant
        ], 201);
    }

    public function edit($id)
    {
        // Mencari merchant berdasarkan ID
        $merchant = Merchant::find($id);

        // Jika merchant tidak ditemukan, kembalikan respon error
        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant tidak ditemukan'
            ], 404);
        }

        // Mengembalikan data merchant untuk diedit
        return response()->json([
            'status' => 'success',
            'message' => 'Data merchant berhasil ditemukan',
            'data' => $merchant
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'merchant_code' => 'required|string|max:100|unique:merchants,merchant_code,' . $id . ',merchant_id', // Validasi merchant_code hanya untuk kode yang belum digunakan oleh merchant lain
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

        $merchant = Merchant::find($id);

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant tidak ditemukan'
            ], 404);
        }

        // Pastikan merchant_code tidak diubah
        if ($request->input('merchant_code') !== $merchant->merchant_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'merchant_code tidak dapat diubah'
            ], 400);
        }

        // Update data merchant lainnya
        $merchant->merchant_name = $request->input('merchant_name');
        $merchant->merchant_address = $request->input('merchant_address');
        $merchant->description = $request->input('description');
        $merchant->updated_by = Auth::check() ? Auth::id() : 1; // Default ke ID 1 jika tidak terautentikasi
        $merchant->updated_at = now();
        $merchant->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Merchant berhasil diperbarui',
            'data' => $merchant
        ], 200);
    }

    public function destroy($id)
    {
        $merchant = Merchant::find($id);

        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant tidak ditemukan'
            ], 404);
        }

        $merchant->deleted_by = Auth::check() ? Auth::id() : 1; // Default ke ID 1 jika tidak terautentikasi
        $merchant->deleted_at = now();
        $merchant->save();
        $merchant->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Merchant berhasil dihapus'
        ], 200);
    }

    //     /**
    //      * Menampilkan daftar semua merchant.
    //      *
    //      * @return \Illuminate\Http\JsonResponse
    //      */
    //     public function index()
    //     {
    //         // Mengambil semua data merchant
    //         $merchants = Merchant::all();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Data merchant berhasil diambil',
    //             'data' => $merchants
    //         ], 200);
    //     }

    //     /**
    //      * Menambahkan merchant baru.
    //      *
    //      * @param  \Illuminate\Http\Request  $request
    //      * @return \Illuminate\Http\JsonResponse
    //      */
    //     public function store(Request $request)
    //     {
    //         // Validasi input
    //         $validator = Validator::make($request->all(), [
    //             'merchant_code' => 'required|string|max:100|unique:merchants',  // Unik untuk setiap merchant
    //             'merchant_name' => 'required|string|max:100',
    //             'merchant_address' => 'required|string',
    //             'description' => 'nullable|string',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => $validator->errors()
    //             ], 400);
    //         }

    //         // Menyimpan merchant baru
    //         $merchant = new Merchant();
    //         $merchant->merchant_code = $request->input('merchant_code');
    //         $merchant->merchant_name = $request->input('merchant_name');
    //         $merchant->merchant_address = $request->input('merchant_address');
    //         $merchant->description = $request->input('description');
    //         $merchant->created_by = Auth::id(); // ID user yang menambahkan
    //         $merchant->created_at = now(); // Waktu pembuatan
    //         $merchant->save();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Merchant berhasil ditambahkan',
    //             'data' => $merchant
    //         ], 201);
    //     }

    //     /**
    //      * Mengupdate data merchant berdasarkan ID.
    //      *
    //      * @param  \Illuminate\Http\Request  $request
    //      * @param  int  $id
    //      * @return \Illuminate\Http\JsonResponse
    //      */
    //     public function update(Request $request, $id)
    // {
    //     // Validasi input
    //     $validator = Validator::make($request->all(), [
    //         'merchant_code' => 'required|string|max:100|unique:merchants',  // Unik untuk setiap merchant
    //         'merchant_name' => 'required|string|max:100',
    //         'merchant_address' => 'required|string',
    //         'description' => 'nullable|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $validator->errors()
    //         ], 400);
    //     }

    //     // Cari merchant berdasarkan ID
    //     $merchant = Merchant::find($id);

    //     if (!$merchant) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Merchant tidak ditemukan'
    //         ], 404);
    //     }

    //     // Update data merchant
    //     $merchant->merchant_code = $request->input('merchant_code');
    //     $merchant->merchant_name = $request->input('merchant_name');
    //     $merchant->merchant_address = $request->input('merchant_address');
    //     $merchant->description = $request->input('description');

    //     // Set updated_by manually (you can replace it with a fixed value or simulate a user ID)
    //     // $merchant->updated_by = 1;  // Set manually, e.g., 1 (for testing or debugging)

    //     $merchant->updated_at = now(); // Update the timestamp for the update action

    //     // Simulate saving the updated merchant
    //     $merchant->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Merchant berhasil diperbarui',
    //         'data' => $merchant
    //     ], 200);
    // }

    //     /**
    //      * Menghapus merchant berdasarkan ID.
    //      *
    //      * @param  int  $id
    //      * @return \Illuminate\Http\JsonResponse
    //      */
    //     public function destroy($id)
    //     {
    //         // Cari merchant berdasarkan ID
    //         $merchant = Merchant::find($id);

    //         if (!$merchant) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'Merchant tidak ditemukan'
    //             ], 404);
    //         }

    //         // Menghapus merchant
    //         $merchant->deleted_by = Auth::id(); // ID user yang menghapus
    //         $merchant->deleted_at = now(); // Waktu penghapusan
    //         $merchant->save();
    //         $merchant->delete();

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Merchant berhasil dihapus'
    //         ], 200);
    //     }
}
