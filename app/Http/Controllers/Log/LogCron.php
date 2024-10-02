<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Log\Class_LogCron;
use Illuminate\Http\Request;

class LogCron extends Controller
{
    public function insertLogCron($request)
    {
        try
        {
            if (isset($request['cron']) && $request['cron'] =='' ) 
            {
                return 'Cron Tidak Boleh Kosong!';
            }
            if (isset($request['message']) && $request['message'] =='' ) 
            {
                return 'Message Tidak Boleh Kosong!';
            }
            if (isset($request['status']) && $request['status'] =='' ) 
            {
                return 'Status Tidak Boleh Kosong!';
            }
            
            $requestModule=[];
            $requestModule['cron'] = $request['cron'];
            $requestModule['signature'] = $request['signature'];
            $requestModule['message'] =  $request['message'];
            $requestModule['status'] = $request['status'];
           
            $classModel = new Class_LogCron();
            $result = $classModel->insert($requestModule);

            return $result;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}
