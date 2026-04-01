<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\Stock\StockGoodsReturn;
use App\Http\Controllers\Model\Stock\StockLog;

use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DateTime;

class Service_StockGoodsReturn extends Controller
{
    public function getStockGoodsReturn(Request $request)
    {
        try
        {
            $module = new StockGoodsReturn();
            $resultModel = $module->getGoodsReturn($request); 
            
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
            $requestModule['service'] = 'Get-GoodsdReturn';
            $requestModule['class'] = 'Service_GoodsReturn';
            $requestModule['function'] = 'getGoodsReturn';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStockGoodsReturn(Request $request)
    {
        try
        {
            $result = [];
            $classModel = new StockGoodsReturn();
            $resultModel = $classModel->insertGoodsReturn($request); 

            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Transaction Goods Return Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created Transaction Goods Return',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-GoodsdReturn';
            $requestModule['class'] = 'Service_GoodsReturn';
            $requestModule['function'] = 'insertGoodsReturn';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStockGoodsReturn(Request $request)
    {
        try
        {
            $stock = new StockGoodsReturn();
            $result['update_stockGoodsReturn'] = $stock->updateGoodsReturn($request); 

            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $request['id'];
            $requestHistory['reff'] = $request['reff'];
            $requestHistory['activity'] = 'Update Stock Goods Return';
            $requestHistory['detail_act'] = json_encode($request->all());

            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Goods Return Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-GoodsReturn';
            $requestModule['class'] = 'Service_GoodsReturn';
            $requestModule['function'] = 'UpdateGoodsReturn';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 

    // public function exportStock(Request $request)
    // {
    //     try
    //     {
    //         $idItem = $request['id_item'];
    //         $itemGroup = $request['item_group'];
    //         $brand = $request['brand'];
    //         $code = $request['code'];
    //         $items = $request['items'];
    //         $unit = $request['unit'];
    //         $haveExp = $request['have_exp'];
     
    //         $param = array(
    //             'id_item' => $idItem,
    //             'item_group' => $itemGroup,
    //             'brand' => $brand,
    //             'code' => $code,
    //             'items' => $items,
    //             'unit' => $unit,
    //             'have_exp' => $haveExp
    //         );
    //         $dateNow = Carbon::now()->format('Y-m-d H:i:s');
    //         return Excel::download(new Export_Stock($param),'Stock-'.$dateNow.'.xlsx');
    //     } catch (\Exception $ex) {
    //         # Insert Log Error
    //         $requestModule=[];
    //         $requestModule['reff'] = 'Service';
    //         $requestModule['service'] = 'Export-Stock';
    //         $requestModule['class'] = 'Service_Stock';
    //         $requestModule['function'] = 'ExportStock';
    //         $requestModule['message'] = $ex->getMessage();
    //         $requestModule['note'] = '-';
    //         $classModel = new LogError();
    //         $result = $classModel->insertLogError($requestModule);
    //         # End Log Error
    //         return $ex;
    //     }  
    // }
}
