<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PtspWidgetController;

// Pembatasan Rate Limiting (Anti-Spam / Anti-Bot)
Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/ptsp/init-data', [PtspWidgetController::class, 'getInitData']);
});

Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/ptsp/store-pengunjung', [PtspWidgetController::class, 'storePengunjung']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});