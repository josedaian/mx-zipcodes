<?php

use App\Http\Controllers\Api\ZipCodeController;
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
Route::get('/', function () {
    return ['success' => true];
});

Route::prefix('zip-codes')->group(function (){

    Route::get('/{zipCode}', [ZipCodeController::class, 'get']);
});
