<?php

namespace App\Http\Controllers\Api;

use App\Models\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PaymentTypeController extends Controller
{
    // Menampilkan semua data payment type
    // public function index()
    // {
    //     // Ambil semua data payment type, termasuk QRIS dan lainnya jika ada
    //     $paymentTypes = PaymentType::all();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $paymentTypes,
    //     ], 200);
    // }

    public function index(Request $request)
    {
        // Get pagination parameters (start and length) from the request
        $start = $request->input('start', 0);   // Default start is 0
        $length = $request->input('length', 10); // Default length is 10
        $search = $request->input('search.value', ''); // Search query, if available

        // Base query for PaymentType with optional join if needed
        $baseQuery = PaymentType::query()
        // Uncomment and adjust join if required
        // ->join('terminals', 'payment_types.terminal_id', '=', 'terminals.terminal_id')
        ->select(
            'payment_types.payment_type_id',
            'payment_types.payment_type_code',
            'payment_types.payment_type_name',
            'payment_types.description'
        );

        // Apply search filter if there’s a search value
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(payment_types.payment_type_code)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(payment_types.payment_type_name)'), 'LIKE', "%" . strtolower($search) . "%")
                    ->orWhere(DB::raw('LOWER(payment_types.description)'), 'LIKE', "%" . strtolower($search) . "%");
                    // ->orWhere(DB::raw('LOWER(terminals.terminal_code)'), 'LIKE', "%" . strtolower($search) . "%");
            });
        }

        // Clone the base query to count filtered records
        $recordsFiltered = $baseQuery->count();

        // Apply pagination to the query
        $data = $baseQuery->offset($start)->limit($length)->get();

        // Count total records without filter for recordsTotal
        $recordsTotal = PaymentType::count();

        // Return data in the desired format for DataTables
        return response()->json([
            'status' => true,
            'message' => 'Data berhasil diambil',
            'recordsTotal' => $recordsTotal,       // Total records in the database
            'recordsFiltered' => $recordsFiltered, // Total records after filtering
            'data' => $data                        // Data to be displayed on the page
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
