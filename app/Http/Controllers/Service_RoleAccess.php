<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\RoleAccess\RoleAccessManagement;

use Carbon\Carbon;
use DateTime;

class Service_RoleAccess extends Controller
{
    public function getRoleAccess(Request $request)
    {
        try
        {
            $module = new RoleAccessManagement();
            $resultModel = $module->getRoleAccess($request); 
            
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
            $requestModule['service'] = 'Get-RoleAccess';
            $requestModule['class'] = 'Service_RoleAccess';
            $requestModule['function'] = 'getRoleAccess';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }
}
