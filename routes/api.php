<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Service_Stock;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::controller(Service_Stock::class)->group(function () {
    Route::get('get_stock', 'getStock');
    Route::post('insert_stock', 'insertStock');
    Route::post('update_stock', 'updateStock');
    // Export
    Route::get('export_stock', 'exportStock');
});