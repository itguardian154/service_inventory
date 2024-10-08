<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;

use App\Http\Controllers\Model\Stock\StockAdjustment;
use App\Http\Controllers\Model\Stock\StockLog;
use App\Exports\Export_StockAdjust;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DateTime;

class Service_StockAdjustment extends Controller
{
    public function getStockAdjustment(Request $request)
    {
        try
        {
            $module = new StockAdjustment();
            $data = $module->getStockAdjustment($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Adjust Successfuly',
                'data' => $data
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Get-StockAdjustment';
            $requestModule['class'] = 'Service_StockAdjustment';
            $requestModule['function'] = 'getStockAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function exportStockAdjustment(Request $request)
    {
        try
        {
            $idItem = $request['id_item'];
            $itemGroup = $request['item_group'];
            $brand = $request['brand'];
            $code = $request['code'];
            $items = $request['items'];
            $unit = $request['unit'];
            $haveExp = $request['have_exp'];
     
            $param = array(
                'id_item' => $idItem,
                'item_group' => $itemGroup,
                'brand' => $brand,
                'code' => $code,
                'items' => $items,
                'unit' => $unit,
                'have_exp' => $haveExp
            );
            $dateNow = Carbon::now()->format('Y-m-d H:i:s');
            return Excel::download(new Export_Stock($param),'Stock Adjustment-'.$dateNow.'.xlsx');
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Export-StockAdjustment';
            $requestModule['class'] = 'Service_StockAdjustment';
            $requestModule['function'] = 'ExportStockAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStockAdjustment(Request $request)
    {
        try
        {
 
            $classModel = new StockAdjustment();
            $result = $classModel->insertAdjustment($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Insert Stock Adjustment Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-StockAdjustment';
            $requestModule['class'] = 'Service_StockAdjustment';
            $requestModule['function'] = 'InsertStockAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStock(Request $request)
    {
        try
        {
            $stock = new Stock();
            $result['update_stock'] = $stock->updateStock($request); 

            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $request['id_item'];
            $requestHistory['reff'] = $request['reff'];
            $requestHistory['activity'] = 'Update Stock Adjustment';
            $requestHistory['detail_act'] = json_encode($request->all());

            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Stock Adjustment Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-StockAdjustment';
            $requestModule['class'] = 'Service_StockAdjustment';
            $requestModule['function'] = 'UpdateStockAdjustment';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 
}
