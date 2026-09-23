<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PtspWidgetController;


Route::get('/ptsp/init-data', [PtspWidgetController::class, 'getInitData']);
Route::post('/ptsp/store-pengunjung', [PtspWidgetController::class, 'storePengunjung']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
