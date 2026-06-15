<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::middleware('api.key')->group(function () {
    Route::get('/sales',   [\App\Http\Controllers\WbDataController::class, 'sales']);
    Route::get('/orders',  [\App\Http\Controllers\WbDataController::class, 'orders']);
    Route::get('/stocks',  [\App\Http\Controllers\WbDataController::class, 'stocks']);
    Route::get('/incomes', [\App\Http\Controllers\WbDataController::class, 'incomes']);
});
