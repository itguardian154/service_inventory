<?php

namespace App\Http\Controllers\Model\UserAccess;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Class_DB\Class_UsersAccessManagement;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

class UserAccessManagement
{
    public function getUserAccessManagement($request)
    {
        try
        {
            $result=[];
            // Get Role Access
            $resultClass = new Class_UsersAccessManagement();
            $resultRoleAccess = $resultClass->show($request);
            $result['get_UserAccessManagement'] = $resultRoleAccess['data'];

            return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Model';
            $requestModule['service'] = 'Get-UsersAccessManagement';
            $requestModule['class'] = 'UsersAccessManagement';
            $requestModule['function'] = 'getUserAccessManagement';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
