<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\Stock\Stock;
use App\Http\Controllers\Model\Stock\StockLog;
use App\Models\stock as model_stock;
use App\Exports\Export_Stock;
use Maatwebsite\Excel\Facades\Excel;

class Service_Stock extends Controller
{
    public function getStock(Request $request)
    {
        try
        {
            $module = new Stock();
            $data = $module->getStock($request); 
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Get Stock Successfuly',
                'data' => $data
            ]);

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
            $result = $classModel->insertLogError($request);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStock(Request $request)
    {
        try
        {
            $idItems = model_stock::max('id') + 1;

            $request['id_item'] = $idItems;
            $ticket = new Stock();
            $result['insert_stock'] = $ticket->insertStock($request); 
     
            // insert History
            $requestHistory = [];
            $requestHistory['id_item'] = $request['id_item'];
            $requestHistory['reff'] = $request['reff'];
            $requestHistory['activity'] = 'Insert Karyawan Employee';
            $requestHistory['detail_act'] = json_encode($request->all());

            $history = new StockLog();
            $result['insert_history'] = $history->insertHistoryStock($requestHistory);
            
            $result=response()->json([
                'status' => 'success',
                'message' => 'Insert Employee Successfuly',
                'data' => $result
            ]);

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
}
