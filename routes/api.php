<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\TerminalController;
use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\TrxController;
use App\Http\Controllers\Api\PaymentTypeController;

// Rute untuk mendapatkan informasi user (autentikasi menggunakan Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/ping/{id}', [TerminalController::class, 'ping']);
Route::get('/cekping/{id}', [TerminalController::class, 'ping']);
Route::get('/check/{id}', [TerminalController::class, 'checkDeviceStatus']);
Route::get('/status/{id}', [TerminalController::class, 'getDeviceStatus']);

// Rute Merchant
Route::get('/merchant', [MerchantController::class, 'index']);
Route::post('/merchant', [MerchantController::class, 'store']);
Route::get('/merchant/code', [MerchantController::class, 'getMerchantCode']);
Route::get('/merchant/{id}', [MerchantController::class, 'edit']);
Route::put('/merchant/{id}', [MerchantController::class, 'update']);
Route::delete('/merchant/{id}', [MerchantController::class, 'destroy']); 

// Rute Terminal
Route::get('/terminal', [TerminalController::class, 'index']);
Route::post('/terminal', [TerminalController::class, 'store']);
Route::get('/terminal/{id}', [TerminalController::class, 'edit']);
Route::put('/terminal/{id}', [TerminalController::class, 'update']);
Route::delete('/terminal/{id}', [TerminalController::class, 'destroy']);

// cms
Route::get('/cms', [CmsController::class, 'index']);
Route::post('/cms', [CmsController::class, 'store']);
Route::put('/cms/{id}', [CmsController::class, 'update']);
Route::delete('/cms/{id}', [CmsController::class, 'destroy']);

// Trx
Route::get('/trx', [TrxController::class, 'index']); // Menampilkan daftar transaksi
Route::post('/trx', [TrxController::class, 'store']); // Menyimpan transaksi baru
Route::put('/trx/{id}', [TrxController::class, 'update']); // Memperbarui transaksi berdasarkan ID
Route::delete('/trx/{id}', [TrxController::class, 'destroy']); // Menghapus transaksi berdasarkan ID

// PaymentType
Route::get('paymentType', [PaymentTypeController::class, 'index']);
Route::get('paymentType/{id}', [PaymentTypeController::class, 'show']);
Route::post('paymentType', [PaymentTypeController::class, 'store']);
Route::put('paymentType/{id}', [PaymentTypeController::class, 'update']);
Route::delete('paymentType/{id}', [PaymentTypeController::class, 'destroy']);

