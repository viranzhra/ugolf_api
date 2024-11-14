<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QRISService;
use Illuminate\Http\Request;
use App\Models\Trx;
use App\Models\Merchant;
use App\Models\Terminal;
use App\Models\PaymentType;
use App\Models\Config;
use App\Models\Cms;
use Illuminate\Support\Facades\Log;


class QRISController extends Controller
{
    private $qrisService;

    public function __construct(QRISService $qrisService)
    {
        $this->qrisService = $qrisService;
    }

        public function generate(Request $request)
        {
            $request->validate([
                'merchantId' => 'required|string',
                'terminalId' => 'required|string',
                'qty' => 'required|integer|min:1'
            ]);

            $transactionData = $request->all();

            $expire = 5; // Waktu kadaluarsa dalam detik

            $merchant = Merchant::where('merchant_code', $transactionData['merchantId'])->first();
            if (!$merchant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Merchant not found'
                ], 404);
            }

            $terminal = Terminal::where('terminal_code', $transactionData['terminalId'])->first();
            if (!$terminal) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terminal not found'
                ], 404);
            }
            
            $merchantId = $merchant->merchant_code;
            $terminalId = $terminal->terminal_code;
            $qty = $transactionData['qty'];
            $amountPerTicket = Cms::where('terminal_id', $terminal->terminal_id)->where('cms_name', 'price')->value('cms_value');
            $totalAmount = $qty * $amountPerTicket;

            // $terminal = Terminal::first();
            $paymentType = PaymentType::first();
        
            // Buat transaksi baru
            $transaction = new Trx();
            $transaction->terminal_id = $terminal->terminal_id;
            $transaction->trx_code = 'TRX' . uniqid(); // time();
            $transaction->trx_reff = 'REF' . time(); // rand(100000, 999999);
            $transaction->payment_type_id = $paymentType->payment_type_id;
            $transaction->amount = $amountPerTicket;
            $transaction->qty = $qty;
            $transaction->total_amount = $totalAmount;
            $transaction->expire = now()->addSeconds($expire); // tidak boleh NULL jadi harus diisi dulu   
            $transaction->trx_date = now(); // terjadinya transaksi
            $transaction->created_by = 1; // user_id

            $transaction->paycode = 'N/A';
            $transaction->payment_status = 'P'; // UNPAID - Pending
            $transaction->payment_date = 'N/A'; // saat dibayar
            $transaction->payment_name = 'N/A';
            $transaction->payment_phone = 'N/A';
            $transaction->reffnumber = 'N/A';

            $transaction->save();

            $trxId = $transaction->trx_code;
            // $trxId = uniqid();
            $amount = $transaction->total_amount;

            $response = $this->qrisService->generateQRIS($merchantId, $terminalId, $trxId, $amount, $expire);

            if ($response['ack'] == '00') {
                $data = json_decode(base64_decode($response['data']), true);

                // Simpan data QRIS ke transaksi
                $transaction->paycode = $data['rawQRIS'];
                $transaction->reffnumber = $data['reffNumber'];
                $transaction->expire = now()->addSeconds((int)$data['expire']); // update expire
                $transaction->save();

                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'transaction' => $transaction,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghasilkan QRIS. Silakan coba lagi.'
                ], 400);
            }        
        }
        
    public function checkStatus(Request $request)
    {
        $request->validate([
            'trxId' => 'required|string',
            'reffNumber' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $trx = Trx::where('trx_code', $request->trxId)
            ->join('terminals', 'trx.terminal_id', '=', 'terminals.terminal_id')
            ->join('merchants', 'terminals.merchant_id', '=', 'merchants.merchant_id')
            ->select('trx.*', 'terminals.terminal_code', 'merchants.merchant_code')
            ->first();

        if (!$trx) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        if ($trx->expire <= now()) {
            return response()->json([
                'ack' => '08',
                'message' => 'Transaction has expired'
            ], 200);
        }

        $merchantId = $trx->merchant_code;
        $terminalId = $trx->terminal_code;

        $trxId = $request->input('trxId');
        $reffNumber = $request->input('reffNumber');
        $amount = $request->input('amount');

        $response = $this->qrisService->checkTransactionStatus($merchantId, $terminalId, $trxId, $reffNumber, $amount);

        // Jika pembayaran berhasil
        if ($response['ack'] == '00') {
            $data = json_decode(base64_decode($response['data']), true);
            $status = $data['status'];

            if ($status == 'PAID') { // Transaksi berhasil
                $transaction = Trx::where('trx_code', $trxId)->first();
                if ($transaction && $transaction->payment_status === 'P') {
                    $transaction->payment_status = 'S'; // Success
                    $transaction->payment_date = now();
                    $transaction->save();
                }            
            }
        }

        return response()->json($response);
    }
}
