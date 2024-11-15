<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
// use Yajra\DataTables\Facades\DataTables;

class TerminalController extends Controller
{

    // Mendapatkan perangkat yang aktif
    public function getDeviceStatus($id)
    {
        $terminal = Terminal::find($id);

        if (!$terminal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terminal tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'status' => $terminal->is_active ? 'Active' : 'Inactive',
                'last_ping' => $terminal->last_ping,
            ]
        ]);
    }

    // Mengecek status perangkat dan memperbarui jika tidak aktif
    public function checkDeviceStatus()
    {
        // Tentukan batas waktu ping untuk menandai perangkat sebagai tidak aktif
        $inactiveThreshold = Carbon::now()->subMinutes(5);

        // Periksa perangkat yang tidak aktif dan update status menjadi tidak aktif
        $inactiveDevices = Terminal::where('last_ping', '<', $inactiveThreshold)
            ->where('is_active', true) // Hanya perangkat yang aktif yang diperbarui
            ->update(['is_active' => false]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status perangkat diperbarui.',
            'inactiveDevices' => $inactiveDevices, // Menampilkan jumlah perangkat yang diubah
        ]);
    }

    // Menangani ping dari perangkat
    // Menangani ping dari perangkat
    public function ping($id)
    {
        // Cari perangkat berdasarkan ID
        $terminal = Terminal::find($id);

        if (!$terminal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terminal tidak ditemukan',
            ], 404);
        }

        // Jika perangkat sudah aktif, tidak perlu memperbarui statusnya
        if ($terminal->is_active) {
            return response()->json([
                'status' => 'success',
                'message' => 'Terminal sudah aktif',
            ]);
        }

        // Perbarui waktu last_ping dan set is_active ke true
        $terminal->last_ping = now();
        $terminal->is_active = true;
        $terminal->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Ping diterima, terminal aktif',
        ]);
    }

    /**
     * Menampilkan daftar semua terminal dengan fitur pencarian dan pagination.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get pagination and search parameters from the request
        $start = $request->input('start', 0); // Default start is 0
        $length = $request->input('length', 10); // Default length is 10
        $search = $request->input('search.value', ''); // Search value if provided

        // Prepare base query
        $baseQuery = DB::table('terminals')
            ->leftJoin('merchants', 'terminals.merchant_id', '=', 'merchants.merchant_id')
            ->select(
                'terminals.*',
                'merchants.merchant_code',
                'terminals.terminal_id as terminal_id',
                'terminals.terminal_code as terminal_kode',
                'terminals.terminal_name as nama_terminal',
                'terminals.terminal_address as alamat_terminal',
                'terminals.description as deskripsi_terminal',
                // 'terminals.status as status_terminal'
                DB::raw('CASE WHEN terminals.is_active = TRUE THEN \'Active\' ELSE \'Inactive\' END as status_terminal')
            );

        // Apply search filter if provided, ensuring case-insensitive search
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where(DB::raw('LOWER(terminals.terminal_code)'), 'LIKE', "%" . strtolower($search) . "%")
                ->orWhere(DB::raw('LOWER(merchants.merchant_code)'), 'LIKE', "%" . strtolower($search) . "%")
                ->orWhere(DB::raw('LOWER(terminals.terminal_name)'), 'LIKE', "%" . strtolower($search) . "%")
                ->orWhere(DB::raw('LOWER(terminals.terminal_address)'), 'LIKE', "%" . strtolower($search) . "%")
                ->orWhere(DB::raw('LOWER(terminals.description)'), 'LIKE', "%" . strtolower($search) . "%")
                ->orWhere(DB::raw('LOWER(terminals.status)'), 'LIKE', "%" . strtolower($search) . "%");
            });
        }

        // Clone the base query to count filtered records
        $recordsFiltered = $baseQuery->count();

        // Apply pagination to the query
        $terminalData = $baseQuery->offset($start)->limit($length)->get();

        // Get the total records count without filters
        $recordsTotal = DB::table('terminals')->count();

        // Return the data in DataTables format
        return response()->json([
            'status' => true,
            'message' => 'Data terminal berhasil diambil',
            'recordsTotal' => $recordsTotal, // Total records in the database
            'recordsFiltered' => $recordsFiltered, // Total records after filtering
            'data' => $terminalData // Data to display on the page
        ], 200);
    }

    /**
     * Menambahkan terminal baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari pengguna
        $request->validate([
            'merchant_code' => 'required|string|exists:merchants,merchant_code',
            'terminal_name' => 'required|string|max:100',
            'terminal_address' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Cari merchant_id berdasarkan merchant_code yang diberikan
        $merchant = Merchant::where('merchant_code', $request->merchant_code)->first();
        
        if (!$merchant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Merchant tidak ditemukan',
            ], 404);
        }

        // Auto-generate kode terminal
        $terminalCode = 'TM-' . strtoupper(Str::random(4)); // Contoh: TM-AB12
        // Cek apakah kode terminal atau kode merchant sudah aktif
        $existingTerminal = Terminal::where('terminal_code', $terminalCode)
            ->orWhere('merchant_id', $merchant->merchant_id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingTerminal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode terminal atau kode merchant sudah aktif',
            ], 400);
        }

        // Simpan terminal baru dengan merchant_id yang ditemukan
        $terminal = Terminal::create([
            'merchant_id' => $merchant->merchant_id,
            'terminal_code' => $terminalCode,
            'terminal_name' => $request->terminal_name,
            'terminal_address' => $request->terminal_address,
            'description' => $request->description,
            'status' => 'aktif', // Set status menjadi aktif
            'created_by' => Auth::check() ? Auth::id() : 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Terminal berhasil ditambahkan',
            'data' => $terminal,
        ], 201);
    }

    public function edit($id)
    {
        // Retrieve the terminal with the specified ID, including the related merchant data
        $terminal = Terminal::with('merchant')->find($id);

        if (!$terminal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terminal not found'
            ], 404);
        }

        // Format the response data to include merchant_code from the related merchant model
        return response()->json([
            'status' => 'success',
            'data' => [
                'terminal_id' => $terminal->id,
                'merchant_id' => $terminal->merchant_id,
                'merchant_code' => $terminal->merchant->merchant_code, // Ensure merchant_code is included
                'terminal_code' => $terminal->terminal_code,
                'terminal_name' => $terminal->terminal_name,
                'terminal_address' => $terminal->terminal_address,
                'description' => $terminal->description,
                'created_by' => $terminal->created_by,
                'created_at' => $terminal->created_at,
                'updated_at' => $terminal->updated_at,
                'status' => $terminal->status,
                // Add other fields as needed
            ]
        ]);


        // // Mencari terminal berdasarkan ID
        // $terminal = Terminal::find($id);

        // // Jika terminal tidak ditemukan, kembalikan respon error
        // if (!$terminal) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'terminal tidak ditemukan'
        //     ], 404);
        // }

        // // Mengembalikan data terminal untuk diedit
        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Data terminal berhasil ditemukan',
        //     'data' => $terminal
        // ], 200);
    }

    // public function edit($id)
    // {
    //     // Fetch the terminal data with merchant details based on ID
    //     $terminal = DB::table('terminals')
    //         ->leftJoin('merchants', 'terminals.merchant_id', '=', 'merchants.merchant_id')
    //         ->where('terminals.terminal_id', $id)
    //         ->select(
    //             'terminals.*',
    //             'merchants.merchant_code',
    //             'terminals.terminal_id as terminal_id',
    //             'terminals.terminal_code as terminal_kode',
    //             'terminals.terminal_name as nama_terminal',
    //             'terminals.terminal_address as alamat_terminal',
    //             'terminals.description as deskripsi_terminal',
    //             DB::raw('CASE WHEN terminals.is_active = TRUE THEN "Active" ELSE "Inactive" END as status_terminal')
    //         )
    //         ->first();

    //     // If the terminal is not found, return an error response
    //     if (!$terminal) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Terminal tidak ditemukan'
    //         ], 404);
    //     }

    //     // Return the terminal data for editing
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Data terminal berhasil ditemukan',
    //         'data' => $terminal
    //     ], 200);
    // }

    /**
     * Mengupdate data terminal berdasarkan ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi input dari pengguna
        $request->validate([
            'terminal_name' => 'required|string|max:100',
            'terminal_address' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Cari terminal berdasarkan ID
        $terminal = Terminal::find($id);

        if (!$terminal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terminal tidak ditemukan',
            ], 404);
        }

        // Menambahkan pengecekan agar kode terminal tidak dapat diubah
        if ($request->input('terminal_code') && $request->input('terminal_code') !== $terminal->terminal_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode terminal tidak dapat diubah',
            ], 400);
        }

        // Update data terminal
        $terminal->terminal_name = $request->input('terminal_name');
        $terminal->terminal_address = $request->input('terminal_address');
        $terminal->description = $request->input('description');
        $terminal->status = 'aktif'; // Set status menjadi aktif
        $terminal->updated_by = Auth::check() ? Auth::id() : 1; // Set ID user yang mengedit
        $terminal->updated_at = now();
        $terminal->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Terminal berhasil diperbarui',
            'data' => $terminal,
        ], 200);
    }

    /**
     * Menghapus terminal berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Cari terminal berdasarkan ID
        $terminal = Terminal::find($id);

        if (!$terminal) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terminal tidak ditemukan',
            ], 404);
        }

        // Hapus terminal dengan soft delete dan set status menjadi non-aktif
        $terminal->deleted_by = Auth::check() ? Auth::id() : 1; // ID user yang menghapus
        $terminal->deleted_at = now(); // Waktu penghapusan
        $terminal->status = 'non-aktif'; // Set status menjadi non-aktif
        $terminal->save();
        $terminal->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Terminal berhasil dihapus',
        ], 200);
    }
}
