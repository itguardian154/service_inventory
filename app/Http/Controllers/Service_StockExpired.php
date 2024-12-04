<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\Stock\StockExpired;

use App\Exports\Export_Stock;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DateTime;

class Service_StockExpired extends Controller
{
    public function getStockExpired(Request $request)
    {
        try
        {
            $module = new StockExpired();
            $resultModel = $module->getStockExpired($request); 

            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Stock Expired Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Get Data',
                    'data' => $resultModel
                ]);
            }

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Get-StockExpired';
            $requestModule['class'] = 'StockExpired';
            $requestModule['function'] = 'getStockExpired';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function exportStockExpired(Request $request)
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

    public function insertStockExpired(Request $request)
    {
        try
        {
 
            $classModel = new StockExpired();
            $result = $classModel->insertStockExpired($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Insert Stock Expired Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-StockExpired';
            $requestModule['class'] = 'Service_StockExpired';
            $requestModule['function'] = 'InsertStockExpired';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStockExpired(Request $request)
    {
        try
        {
            $classModel = new StockExpired();
            $result = $classModel->updateStockExpired($request); 

            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Stock Expired Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-StockExpired';
            $requestModule['class'] = 'Service_StockExpired';
            $requestModule['function'] = 'UpdateStockExpired';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 
}
