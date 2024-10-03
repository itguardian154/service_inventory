<?php

namespace App\Http\Controllers\Model\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Class_DB\Class_StockLog;
use App\Http\Controllers\Log\LogError;

use Carbon\Carbon;

class StockLog extends Controller
{
    public function insertHistoryStock($request)
    {
        try
        {
            if (isset($request['reff']) && $request['reff'] =='' ) 
            {
                return 'Reff Tidak Boleh Kosong!';
            }
            if (isset($request['activity']) && $request['activity'] =='' ) 
            {
                return 'Activity Tidak Boleh Kosong!';
            }
            if (isset($request['activity']) && $request['activity'] =='' ) 
            {
                return 'Activity Tidak Boleh Kosong!';
            }
            if (isset($request['detail_act']) && $request['detail_act'] =='' ) 
            {
                return 'Detail Activity Tidak Boleh Kosong!';
            }

            $requestModule=[];
            $requestModule['id_item'] = $request['id_item'];
            $requestModule['reff'] =  $request['reff'];
            $requestModule['activity'] = $request['activity'];
            $requestModule['detail_act'] = $request['detail_act'];
            $requestModule['years'] =  Carbon::now()->format('Y');

            $classModel = new Class_StockLog();
            $resultHistory = $classModel->insert($requestModule);

            return $resultHistory;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Model';
            $requestModule['service'] = '-';
            $requestModule['class'] = 'StockLog';
            $requestModule['function'] = 'insertHistoryStock';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }

}
