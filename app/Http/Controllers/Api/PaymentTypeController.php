<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PaymentTypeController extends Controller
{
    // Menampilkan semua data payment type
    public function index()
    {
        // Ambil semua data payment type, termasuk QRIS dan lainnya jika ada
        $paymentTypes = PaymentType::all();

        return response()->json([
            'success' => true,
            'data' => $paymentTypes,
        ], 200);
    }

    // Menampilkan detail payment type berdasarkan ID
    public function show($id)
    {
        // Cari payment type berdasarkan ID
        $paymentType = PaymentType::find($id);

        if ($paymentType) {
            return response()->json([
                'success' => true,
                'data' => $paymentType,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment type not found',
        ], 404);
    }

    // Menyimpan payment type baru
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'payment_type_code' => 'required|unique:payment_types',
            'payment_type_name' => 'required',
            'description' => 'nullable',
        ]);

        // Menyimpan data baru
        $paymentType = PaymentType::create([
            'payment_type_code' => $request->payment_type_code,
            'payment_type_name' => $request->payment_type_name,
            'description' => $request->description,
            'created_by' => Auth::check() ? Auth::id() : 1, // Id user yang membuat data
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment type successfully created!',
            'data' => $paymentType,
        ], 201);
    }

    // Menyimpan perubahan data payment type
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'payment_type_code' => 'required|unique:payment_types,payment_type_code,' . $id,
            'payment_type_name' => 'required',
            'description' => 'nullable',
        ]);

        // Cari payment type berdasarkan ID
        $paymentType = PaymentType::findOrFail($id);

        // Update data payment type
        $paymentType->update([
            'payment_type_code' => $request->payment_type_code,
            'payment_type_name' => $request->payment_type_name,
            'description' => $request->description,
            'updated_by' => Auth::check() ? Auth::id() : 1, // Id user yang mengubah data
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment type successfully updated!',
            'data' => $paymentType,
        ], 200);
    }

    // Menghapus payment type
    public function destroy($id)
    {
        // Cari payment type berdasarkan ID
        $paymentType = PaymentType::findOrFail($id);

        // Hapus data payment type
        $paymentType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment type successfully deleted!',
        ], 200);
    }
}
