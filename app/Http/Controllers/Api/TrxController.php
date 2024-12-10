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
            ->selectRaw('EXTRACT(YEAR FROM trx_date) as year, EXTRACT(MONTH FROM trx_date) as month, SUM(qty) as total_quantity')
            ->groupByRaw('EXTRACT(YEAR FROM trx_date), EXTRACT(MONTH FROM trx_date)')
            ->orderByRaw('EXTRACT(YEAR FROM trx_date), EXTRACT(MONTH FROM trx_date)')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => (int) $item->month,  // Pastikan bulan berupa integer
                    'year' => (int) $item->year,    // Pastikan tahun berupa integer
                    'total_quantity' => (int) $item->total_quantity,
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

    public function getWeeklyData(Request $request)
    {
        // Mengambil transaksi berdasarkan status pembayaran 'P' dan 'S'
        $transactions = Trx::whereIn('payment_status', ['P', 'S'])
            ->selectRaw("
        TO_CHAR(created_at, 'Day') as day,
        payment_status,
        SUM(qty) as total_quantity
    ")
            ->groupByRaw("TO_CHAR(created_at, 'Day'), payment_status")
            ->orderByRaw("MIN(created_at)") // Urutkan sesuai urutan hari
            ->get();

        // Menyusun data per hari
        $dailyData = [
            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            'data' => [
                'S' => array_fill(0, 7, 0), // Untuk payment_status 'S'
                'P' => array_fill(0, 7, 0)  // Untuk payment_status 'P'
            ],
        ];

        foreach ($transactions as $transaction) {
            // Cocokkan hari untuk memasukkan data ke dalam array
            $dayIndex = array_search(trim($transaction->day), array_map('ucfirst', $dailyData['days']));
            if ($dayIndex !== false) {
                $dailyData['data'][$transaction->payment_status][$dayIndex] = (int) $transaction->total_quantity;
            }
        }

        // Kembalikan response JSON
        return response()->json([
            'status' => true,
            'message' => 'Data harian berhasil diambil',
            'data' => $dailyData,
        ]);
    }

    public function getDailyCounts()
    {
        // Ambil jumlah transaksi unik berdasarkan trx_code setiap harinya
        $dailyQuantities = DB::table('trx')
            ->selectRaw('DATE(trx_date) as trx_date, COUNT(DISTINCT trx_code) as unique_transactions, SUM(total_amount) as total_amount')
            ->groupByRaw('DATE(trx_date)')  // Mengelompokkan berdasarkan tanggal (tanpa waktu)
            ->orderByRaw('DATE(trx_date)')   // Mengurutkan berdasarkan tanggal
            ->get()
            ->map(function ($item) {
                return [
                    'trx_date' => $item->trx_date,  // Tanggal transaksi
                    'unique_transactions' => (int) $item->unique_transactions,  // Jumlah transaksi unik per tanggal
                    'total_amount' => (float) $item->total_amount,    // Total nominal per tanggal
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Data jumlah transaksi per hari berhasil diambil',
            'data' => $dailyQuantities,
        ], 200);
    }

    public function getTrxData()
    {
        // Ambil total penjualan minggu ini
        $trxThisWeek = DB::table('trx')
            ->whereBetween('trx_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('total_amount');

        // Ambil persentase perubahan dibandingkan minggu lalu
        $trxLastWeek = DB::table('trx')
            ->whereBetween('trx_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('total_amount');

        $percentageChange = $trxLastWeek > 0 ? (($trxThisWeek - $trxLastWeek) / $trxLastWeek) * 100 : 0;

        // Mengambil data transaksi mingguan (bisa ditambahkan lebih banyak data jika diperlukan)
        return response()->json([
            'status' => true,
            'message' => 'Data penjualan berhasil diambil',
            'data' => [
                'trx_this_week' => number_format($trxThisWeek, 2),  // Format untuk uang
                'trx_last_week' => number_format($trxLastWeek, 2),
                'percentage_change' => number_format($percentageChange, 2), // Format persentase
                'transactions_count' => DB::table('trx')
                    ->whereBetween('trx_date', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count('trx_code'), // Jumlah transaksi minggu ini
            ],
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

    // public function destroy($trx_id)
    // {
    //     $transaction = Trx::findOrFail($trx_id);
    //     $transaction->delete();
    //     return response()->json(['message' => 'Transaksi berhasil dihapus!']);
    // }
}
