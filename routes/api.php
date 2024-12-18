<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\TerminalController;
use App\Http\Controllers\Api\CmsController;
use App\Http\Controllers\Api\TrxController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthJWTController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\QRISController;

/* Autentikasi JWT */
Route::post('register', [AuthJWTController::class, 'register']);
Route::post('login', [AuthJWTController::class, 'login']);
Route::middleware(['jwt.verify'])->get('/user', [AuthJWTController::class, 'getUserData']);
Route::get('/profile', [AuthController::class, 'profile']);

// Rute untuk mendapatkan informasi user (autentikasi menggunakan Sanctum)
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/profile', [AuthController::class, 'profile']);

    // Route::post('/ping/{id}', [TerminalController::class, 'ping']);
    // Route::get('/cekping/{id}', [TerminalController::class, 'ping']);
    // Route::get('/check/{id}', [TerminalController::class, 'checkDeviceStatus']);
    // Route::get('/status/{id}', [TerminalController::class, 'getDeviceStatus']);

    // // Rute Merchant
    // Route::get('/merchant', [MerchantController::class, 'index']);
    // Route::post('/merchant', [MerchantController::class, 'store']);
    // Route::get('/merchant/code', [MerchantController::class, 'getMerchantCode']);
    // Route::get('/merchant/{id}', [MerchantController::class, 'edit']);
    // Route::put('/merchant/{id}', [MerchantController::class, 'update']);
    // Route::delete('/merchant/{id}', [MerchantController::class, 'destroy']); 

    // // Rute Terminal
    // Route::get('/terminal', [TerminalController::class, 'index']);
    // Route::post('/terminal', [TerminalController::class, 'store']);
    // Route::get('/terminal/{id}', [TerminalController::class, 'edit']);
    // Route::put('/terminal/{id}', [TerminalController::class, 'update']);
    // Route::delete('/terminal/{id}', [TerminalController::class, 'destroy']);

    // // cms
    // Route::get('/cms', [CmsController::class, 'index']);
    // Route::post('/cms', [CmsController::class, 'store']);
    // Route::put('/cms/{id}', [CmsController::class, 'update']);
    // Route::delete('/cms/{id}', [CmsController::class, 'destroy']);

    // // cms
    // Route::get('/config', [ConfigController::class, 'index']);
    // Route::post('/config', [ConfigController::class, 'store']);
    // Route::put('/config/{id}', [ConfigController::class, 'update']);
    // Route::delete('/config/{id}', [ConfigController::class, 'destroy']);

    // // Trx
    // Route::get('/trx', [TrxController::class, 'index']); // Menampilkan daftar transaksi
    // Route::post('/trx', [TrxController::class, 'store']); // Menyimpan transaksi baru
    // Route::put('/trx/{id}', [TrxController::class, 'update']); // Memperbarui transaksi berdasarkan ID
    // Route::delete('/trx/{id}', [TrxController::class, 'destroy']); // Menghapus transaksi berdasarkan ID

    // // PaymentType
    // Route::get('paymentType', [PaymentTypeController::class, 'index']);
    // Route::get('paymentType/{id}', [PaymentTypeController::class, 'show']);
    // Route::post('paymentType', [PaymentTypeController::class, 'store']);
    // Route::put('paymentType/{id}', [PaymentTypeController::class, 'update']);
    // Route::delete('paymentType/{id}', [PaymentTypeController::class, 'destroy']);
// });


Route::post('/ping/{id}', [TerminalController::class, 'ping']);
Route::get('/cekping/{id}', [TerminalController::class, 'ping']);
Route::post('/ping', [TerminalController::class, 'ping']);
// Route::get('/cekping/{id}', [TerminalController::class, 'ping']);
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
Route::get('/cms/get-by-code/{terminal_code}', [CmsController::class, 'getByTerminalCode']);
Route::post('/cms', [CmsController::class, 'store']);
Route::put('/cms/{id}', [CmsController::class, 'update']);
Route::delete('/cms/{id}', [CmsController::class, 'destroy']);

// cms
Route::get('/config', [ConfigController::class, 'index']);
Route::post('/config', [ConfigController::class, 'store']);
Route::put('/config/{id}', [ConfigController::class, 'update']);
Route::delete('/config/{id}', [ConfigController::class, 'destroy']);

// Trx
Route::get('/trx', [TrxController::class, 'index']); // Menampilkan daftar transaksi
Route::get('/trx/{id}', [TrxController::class, 'show']); // Menampilkan daftar transaksi
Route::post('/trx', [TrxController::class, 'store']); // Menyimpan transaksi baru
Route::put('/trx/{id}', [TrxController::class, 'update']); // Memperbarui transaksi berdasarkan ID
Route::delete('/trx/{id}', [TrxController::class, 'destroy']); // Menghapus transaksi berdasarkan ID
Route::get('/getWeeklyData', [TrxController::class, 'getWeeklyData']);
// Route::get('/getDetail/{id}', [TrxController::class, 'show']);
Route::get('/trxDailyCount', [TrxController::class, 'getDailyCounts']);
Route::get('/trxSalesData', [TrxController::class, 'getSalesData']);

// PaymentType
Route::get('paymentType', [PaymentTypeController::class, 'index']);
Route::get('paymentType/{id}', [PaymentTypeController::class, 'show']);
Route::post('paymentType', [PaymentTypeController::class, 'store']);
Route::put('paymentType/{id}', [PaymentTypeController::class, 'update']);
Route::delete('paymentType/{id}', [PaymentTypeController::class, 'destroy']);

Route::post('/frontend/init', [FrontEndController::class, 'init']);
// QRIS
Route::post('/qris/generate', [QRISController::class, 'generate'])->name('qris.generate');
Route::post('/qris/check-status', [QRISController::class, 'checkStatus'])->name('qris.check_status');
// Route::post('/qris/callback', [QRISController::class, 'callback'])->name('qris.callback');

// QRIS Payment
Route::post('/qris/generate', [QRISController::class, 'generate'])->name('qris.generate');
Route::post('/qris/check-status', [QRISController::class, 'checkStatus'])->name('qris.check_status');

// Route::group(['middleware' => ['jwt.verify']], function () {
    // Role Management
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/create', [RoleController::class, 'create']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit']);
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('/roles/assign', [RoleController::class, 'indexAssignRole']);
    Route::put('/roles/assign/{user}', [RoleController::class, 'assignRole'])->name('roles.assign');
    Route::delete('/roles/assign/{user}', [RoleController::class, 'userDestroy'])->name('roles.assign.destroy');
    Route::post('/roles/user', [RoleController::class, 'storeUser']);
// });

/* User Management */
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::put('/user/update', [ProfileController::class, 'update']);
    Route::post('/user/update-photo', [ProfileController::class, 'updatePhoto']);
    Route::delete('/delete/profile/photo', [ProfileController::class, 'deletePhoto']);
});