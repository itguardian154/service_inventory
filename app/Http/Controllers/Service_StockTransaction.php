<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\Stock\StockTransaction;
use App\Models\stock_transaction;

use Carbon\Carbon;
use DateTime;

class Service_StockTransaction extends Controller
{
    public function getStockTransaction(Request $request)
    {
        try
        {
            $module = new StockTransaction();
            $resultModel = $module->getStockTransaction($request); 
            
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
            $requestModule['service'] = 'Get-StockTransaction';
            $requestModule['class'] = 'Service_StockTransaction';
            $requestModule['function'] = 'getStockTransaction';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStockTransaction(Request $request)
    {
        try
        {
          
            $result = [];
            // cek type insert 
            $jsonInput='';
            if (isset($request['json_input']) && $request['json_input']!='' ) 
            {
                $classModel = new StockTransaction();
                $resultModel = $classModel->jsonInsertStockTransaction($request); 
            }
            else
            {
                $classModel = new StockTransaction();
                $resultModel = $classModel->insertStockTransaction($request); 
            }
            
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
            $requestModule['service'] = 'Insert-StockTransaction';
            $requestModule['class'] = 'Service_StockTransaction';
            $requestModule['function'] = 'InsertStockTransaction';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStockTransaction(Request $request)
    {
        try
        {
          
            $result = [];
            // cek type insert 
            $jsonInput='';
            if (isset($request['json_input']) && $request['json_input']!='' ) 
            {
                $classModel = new StockTransaction();
                $resultModel = $classModel->jsonUpdateStockTransaction($request); 
            }
            else
            {
                $classModel = new StockTransaction();
                $resultModel = $classModel->updateStockTransaction($request); 
            }
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Updated Transaction Items Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Updated Transaction Items',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-StockTransaction';
            $requestModule['class'] = 'Service_StockTransaction';
            $requestModule['function'] = 'updateStockTransaction';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }
}
