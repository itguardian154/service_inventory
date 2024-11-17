<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Service_Stock;
use App\Http\Controllers\Service_StockAdjustment;
use App\Http\Controllers\Service_StockGoodsReturn;

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

# STOCK
Route::controller(Service_Stock::class)->group(function () {
    Route::get('get_stock', 'getStock');
    Route::post('insert_stock', 'insertStock');
    Route::post('update_stock', 'updateStock');

    // Export
    Route::get('export_stock', 'exportStock');
});

Route::controller(Service_StockAdjustment::class)->group(function () {
    Route::get('get_stock_adjustment', 'getStockAdjustment');
    Route::post('insert_stock_adjustment', 'insertStockAdjustment');
    Route::post('update_stock_adjustment', 'updateStockAdjustment');

    // Export
    Route::get('export_stock_adjustment', 'exportStockAdjustment');
});

Route::controller(Service_StockGoodsReturn::class)->group(function () {
    Route::get('get_stock_goods_return', 'getStockGoodsReturn');
    Route::post('insert_stock_goods_return', 'insertStockGoodsReturn');
    Route::post('update_stock_goods_return', 'updateStockGoodsReturn');

    // Export
    Route::get('export_stock_goods_return', 'exportStockGoodsReturn');
});


# END STOCK