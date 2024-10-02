<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Service_Stock extends Controller
{
    public function getStock(Request $request)
    {
        try
        {
            $module = new Employee();
            $data = $module->getEmployee($request); 
            
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
}
