<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MerchantController;
// use App\Http\Controllers\Api\CmsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/cms', [CmsController::class, 'index']);
// Route::put('/cms/{id}', [CmsController::class, 'update']);
// Route::post('/cms', [CmsController::class, 'store']);

Route::get('/merchant', [MerchantController::class, 'index']);
Route::post('/merchant', [MerchantController::class, 'store']);
Route::put('/merchant/{id}', [MerchantController::class, 'update']);