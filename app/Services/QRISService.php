<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QRISService
{
    private $baseUrl;
    private $merchantId;
    private $terminalId;
    private $secretKey;
    private $authorization;

    public function __construct()
    {
        $this->baseUrl = config('services.qris.base_url');
        // $this->merchantId = config('services.qris.merchant_id');
        // $this->terminalId = config('services.qris.terminal_id');
        $this->secretKey = config('services.qris.secret_key');
        $this->authorization = config('services.qris.authorization');
    }

    public function generateQRIS(string $feCode, string $merchantId, string $terminalId, string $trxId, string $amount, string $expire)
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $body = [
            "merchantId" => $merchantId,
            "terminalId" => $terminalId,
            "trxId" => $trxId,
            "amount" => $amount,
            "expire" => $expire,
            "posId" => $feCode,
            "timestamp" => $timestamp,
        ];

        $sign = $merchantId .
                $terminalId .
                $body['posId'] .
                $trxId .
                $amount .
                $expire .
                $timestamp .
                $this->secretKey;

        // dd($expire);
        $signature = hash('sha256', $sign);

        $body['signature'] = $signature;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $this->authorization,
        ])->post("{$this->baseUrl}/generate", $body);

        return $response->json();
        
        // dd($response->json());    
    }

    public function checkTransactionStatus(string $feCode, string $merchantId, string $terminalId, string $trxid, string $reffNumber, string $amount)
    {
        $timestamp = now()->format('Y-m-d H:i:s');

        // $trxid = "100979967685911";
        // $reffNumber = "2024111314160700243860065";
        // $amount = "8000";

        $body = [
            "trxid" => $trxid,
            "reffNumber" => $reffNumber,
            "merchantId" => $merchantId,
            "terminalId" => $terminalId,
            "amount" => $amount,
            "posId" => $feCode,
            "timestamp" => $timestamp,
        ];

        $sign = $trxid .
                $reffNumber .
                $merchantId .
                $terminalId .
                $amount .
                $body['posId'] .
                $timestamp .
                $this->secretKey;

        $signature = hash('sha256', $sign);
        $body['signature'] = $signature;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . $this->authorization,
        ])->post("{$this->baseUrl}/CheckStatus", $body);

        return $response->json();
    }

}
