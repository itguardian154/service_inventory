<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Service_Stock;
use App\Http\Controllers\Service_StockTransaction;
use App\Http\Controllers\Service_StockAdjustment;
use App\Http\Controllers\Service_StockGoodsReturn;
use App\Http\Controllers\Service_StockExpired;
use App\Http\Controllers\Service_ReceiveOrder;
use App\Http\Controllers\Service_StoreRequest;
use App\Http\Controllers\Service_RoleAccess;
use App\Http\Controllers\Service_UserAccessManagement;
use App\Http\Controllers\UpdateStockDataController;
use App\Http\Controllers\Exports\ReceiveOrderController;

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
    Route::post('insert_stock_db', 'insertStockDB');
    Route::post('update_stock', 'updateStock');

    // Export
    Route::get('export_stock', 'exportStock');
    Route::get('export_inventory_valuation_report', 'exportInventoryValuationReport');
});

Route::controller(Service_StockTransaction::class)->group(function () {
    Route::get('get_stockTransaction', 'getStockTransaction');
    Route::post('insert_stockTransaction', 'insertStockTransaction');
    Route::post('update_stockTransaction', 'updateStockTransaction');

    // Export
    Route::get('export_stockTransaction', 'exportStockTransaction');
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

Route::controller(Service_StockExpired::class)->group(function () {
    Route::get('get_stock_expired', 'getStockExpired');
    Route::post('insert_stock_expired', 'insertStockExpired');
    Route::post('update_stock_expired', 'updateStockExpired');
    // Export
    Route::get('export_stock_expired', 'exportStockExpired');
});
# END STOCK

# RECEIVE ORDER
Route::controller(Service_ReceiveOrder::class)->group(function () {
    Route::get('get_receive_order', 'getReceiveOrder');
    Route::post('insert_receive_order', 'insertReceiveOrder');
    Route::post('update_receive_order', 'updateReceiveOrder');
    Route::post('update_receive_order_item_detail', 'updateReceiveOrderItemDetail');

    // Export
    Route::get('export_receive_order', 'exportReceiveOrder');
});
# END RECEIVE ORDER

# STORE REQUEST
Route::controller(Service_StoreRequest::class)->group(function () {
    Route::get('get_store_request', 'getStoreRequest');
    Route::post('insert_store_request', 'insertStoreRequest');
    Route::post('update_store_request', 'updateStoreRequest');
    Route::post('update_distribution_status', 'updateDistributionStatus');
    // Export
    Route::get('export_store_request', 'exportStoreRequest');
});
# END STORE REQUEST

# ROLE ACCESS
Route::controller(Service_RoleAccess::class)->group(function () {
    Route::get('get_role_access', 'getRoleAccess');
});
# END ROLE ACCESS

# USERS ACCESS MANAGEMENT
Route::controller(Service_UserAccessManagement::class)->group(function () {
    Route::get('get_user_access_management', 'userAccessManagement');
});
# END USERS ACCESS MANAGEMENT


# UPDATE STOCK From Gsheet
Route::controller(UpdateStockDataController::class)->group(function () {
    Route::post('update-stock-name', 'updateStock');
});

# EXPORT
Route::get('/receive-order/export', [ReceiveOrderController::class, 'export']);