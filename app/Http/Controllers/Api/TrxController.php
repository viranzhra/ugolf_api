<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trx;
use App\Models\PaymentType;
use App\Models\Terminal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrxController extends Controller
{
    // Menampilkan daftar transaksi
    // public function index()
    // {
    //     // Ambil semua transaksi dengan hubungan ke terminal dan payment_type
    //     $transactions = Trx::with(['terminal', 'paymentType'])->get();

    //     // Mengembalikan data transaksi dalam format JSON
    //     return response()->json($transactions);
    // }

    public function index(Request $request)
    {
        // Ambil parameter pagination dan search dari request
        $start = $request->input('start', 0); // Default start adalah 0
        $length = $request->input('length', 10); // Default length adalah 10
        $search = $request->input('search.value', ''); // Nilai pencarian jika ada

        // // Query dasar untuk transaksi dengan hubungan ke terminal dan payment_type
        // $baseQuery = Trx::with(['terminal', 'paymentType'])->select(
        //     'trx_id',
        //     'trx_code',
        //     'trx_reff',
        //     'amount',
        //     'qty',
        //     'total_amount',
        //     'paycode',
        //     'expire',
        //     'trx_date',
        //     'payment_status'
        // );

        // Prepare base query
        $baseQuery = DB::table('trx')
            ->leftJoin('terminals', 'trx.terminal_id', '=', 'terminals.terminal_id')
            ->leftJoin('payment_types', 'trx.payment_type_id', '=', 'payment_types.payment_type_id')
            ->select(
                // trx fields
                'trx.trx_id',
                'trx.trx_code',
                'trx.trx_reff',
                'trx.amount',
                'trx.qty',
                'trx.total_amount',
                'trx.paycode',
                'trx.expire',
                'trx.trx_date',
                'trx.payment_status',
                'trx.payment_date',
                'trx.payment_name',
                'trx.payment_phone',
                'trx.reffnumber',
                'trx.issuer_reffnumber', // nomor referensi yang diberikan oleh issuer (pihak penerbit) dalam konteks transaksi
                'trx.created_by',
                'trx.updated_by',
                'trx.deleted_by',
                'trx.deleted_at',
                // terminal fields
                'terminals.terminal_id as terminal_id',
                'terminals.terminal_code as terminal_kode',
                'terminals.terminal_name as nama_terminal',
                'terminals.terminal_address as alamat_terminal',
                'terminals.description as deskripsi_terminal',
                // payment type fields
                'payment_types.payment_type_code as payment_type_code',
                'payment_types.payment_type_name as payment_type_name',
                'payment_types.description as payment_type_description'
            );

        // Menambahkan filter pencarian jika ada
        if ($search) {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('trx_code', 'LIKE', "%" . strtolower($search) . "%")
                        ->orWhere('trx_reff', 'LIKE', "%" . strtolower($search) . "%")
                        ->orWhere('paycode', 'LIKE', "%" . strtolower($search) . "%");
            });
        }

        // Menghitung jumlah transaksi yang terfilter (tanpa pagination)
        $recordsFiltered = $baseQuery->count();

        // Menambahkan pagination
        $transactions = $baseQuery->offset($start)->limit($length)->get();

        // Menghitung total transaksi tanpa filter (untuk recordsTotal)
        $recordsTotal = DB::table('trx')->count();

        // Menghitung jumlah total quantity dari semua transaksi
        $totalQuantity = DB::table('trx')->sum('qty');

        // Hitung total jumlah nominal transaksi
        $totalAmount = $transactions->sum('total_amount');

        // Menghitung jumlah tiket terjual (transaksi yang berhasil)
        $ticketSold = $transactions->count();

        // Perhitungan total quantity per bulan
        $monthlyQuantities = DB::table('trx')
            ->selectRaw('MONTH(trx_date) as month, SUM(qty) as total_quantity')
            ->groupByRaw('MONTH(trx_date)')
            ->orderByRaw('MONTH(trx_date)')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => $item->month,
                    'total_quantity' => $item->total_quantity,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Data transaksi berhasil diambil',
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'totalQuantity' => $totalQuantity, // Total quantity
            'totalAmount' => $totalAmount,  // Total nominal
            'ticketSold' => $ticketSold,
            'data' => $transactions,
            'monthlyQuantities' => $monthlyQuantities,
        ], 200);
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|exists:terminals,terminal_id',
            'trx_code' => 'required|string|max:100',
            'trx_reff' => 'required|string|max:100',
            'payment_type_id' => 'required|exists:payment_types,payment_type_id',
            'amount' => 'required|integer',
            'qty' => 'required|integer',
            'paycode' => 'required|string',
            'expire' => 'nullable|date_format:Y-m-d H:i:s',
            'trx_date' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $totalAmount = $validated['amount'] * $validated['qty'];

        $transaction = Trx::create([
            'terminal_id' => $validated['terminal_id'],
            'trx_code' => $validated['trx_code'],
            'trx_reff' => $validated['trx_reff'],
            'payment_type_id' => $validated['payment_type_id'],
            'amount' => $validated['amount'],
            'qty' => $validated['qty'],
            'total_amount' => $totalAmount,
            'paycode' => $validated['paycode'],
            'expire' => $validated['expire'],
            'trx_date' => $validated['trx_date'] ?: Carbon::now(),
            'payment_status' => 'Belum Dibayar', // Status Belum Dibayar
            'created_by' => Auth::check() ? Auth::id() : 1,
        ]);

        return response()->json($transaction, 201);
    }

    public function updatePaymentStatus(Request $request, $trx_id)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date_format:Y-m-d H:i:s',
            'payment_status' => 'required|in:Sudah Dibayar,Belum Dibayar',
            'payment_name' => 'nullable|string',
            'payment_phone' => 'nullable|string',
            'reffnumber' => 'nullable|string',
            'issuer_reffnumber' => 'nullable|string',
        ]);

        $transaction = Trx::findOrFail($trx_id);

        $transaction->update([
            'payment_date' => $validated['payment_date'],
            'payment_status' => $validated['payment_status'],
            'payment_name' => $validated['payment_name'],
            'payment_phone' => $validated['payment_phone'],
            'reffnumber' => $validated['reffnumber'],
            'issuer_reffnumber' => $validated['issuer_reffnumber'],
            'updated_by' => Auth::check() ? Auth::id() : 1,
        ]);

        if ($validated['payment_status'] === 'Sudah Dibayar') {
            $transaction->update([
                'payment_status' => 'Sudah Dibayar',
            ]);
        }

        return response()->json($transaction);
    }

    public function show($trx_id)
    {
        $transaction = DB::table('trx')
            ->leftJoin('terminals', 'trx.terminal_id', '=', 'terminals.terminal_id')
            ->leftJoin('payment_types', 'trx.payment_type_id', '=', 'payment_types.payment_type_id')
            ->select(
                'trx.trx_id',
                'trx.trx_code',
                'trx.trx_reff',
                'trx.amount',
                'trx.qty',
                'trx.total_amount',
                'trx.paycode',
                'trx.expire',
                'trx.trx_date',
                'trx.payment_status',
                'trx.payment_date',
                'trx.payment_name',
                'trx.payment_phone',
                'trx.reffnumber',
                'trx.issuer_reffnumber',
                'terminals.terminal_name',
                'payment_types.payment_type_name'
            )
            ->where('trx.trx_id', $trx_id)
            ->first(); // Get the first result

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function destroy($trx_id)
    {
        $transaction = Trx::findOrFail($trx_id);
        $transaction->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus!']);
    }
}
