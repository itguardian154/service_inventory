<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\Stock\Stock;
use App\Http\Controllers\Model\Stock\StockLog;
use App\Models\stock as model_stock;

use App\Exports\Export_Stock;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DateTime;

class Service_Stock extends Controller
{
    public function getStock(Request $request)
    {
        try
        {
            $module = new Stock();
            $resultModel = $module->getStock($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Get Data Successfuly',
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
            $requestModule['service'] = 'Get-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'getStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStock(Request $request)
    {
        try
        {
          
            $result = [];
            $classModel = new Stock();
            $resultModel = $classModel->insertStock($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Transaction Items Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created Transaction Items',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
        
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'InsertStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function exportStock(Request $request)
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
            return Excel::download(new Export_Stock($param),'Stock-'.$dateNow.'.xlsx');
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Export-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'ExportStock';
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
            $requestHistory['id_item'] = $request['id'];
            $requestHistory['reff'] = $request['reff'];
            $requestHistory['activity'] = 'Update Stock';
            $requestHistory['detail_act'] = json_encode($request->all());

            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Stock Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-Stock';
            $requestModule['class'] = 'Service_Stock';
            $requestModule['function'] = 'UpdateStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 
}
