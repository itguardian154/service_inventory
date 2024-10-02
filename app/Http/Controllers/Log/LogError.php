<?php

namespace App\Http\Controllers\Log;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Log\Class_LogError;
use Illuminate\Http\Request;


class LogError extends Controller
{
    public function insertLogError($request)
    {
        try
        {
            if (isset($request['reff']) && $request['reff'] =='' ) 
            {
                return 'Reff Tidak Boleh Kosong!';
            }
            if (isset($request['service']) && $request['service'] =='' ) 
            {
                return 'Service Tidak Boleh Kosong!';
            }
            if (isset($request['Class']) && $request['Class'] =='' ) 
            {
                return 'Class Tidak Boleh Kosong!';
            }
            if (isset($request['function']) && $request['function'] =='' ) 
            {
                return 'Function Tidak Boleh Kosong!';
            }
            if (isset($request['message']) && $request['message'] =='' ) 
            {
                return 'Message Tidak Boleh Kosong!';
            }
            
            $requestModule=[];
            $requestModule['reff'] = $request['reff'];
            $requestModule['service'] =  $request['service'];
            $requestModule['class'] = $request['class'];
            $requestModule['function'] = $request['function'];
            $requestModule['message'] = $request['message'];
            $requestModule['note'] = $request['note'];

            $classModel = new Class_LogError();
            $resultHistory = $classModel->insert($requestModule);

            return $resultHistory;
        } catch (\Exception $ex) {
            return $ex;
        }
    }
}