<?php

namespace App\Http\Controllers\Model\RoleAccess;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Class_DB\Class_RoleAccessManagement;
use App\Http\Controllers\Class_DB\Class_RoleAccessDetail;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;
use Exception;

class RoleAccessManagement
{
    public function getRoleAccess($request)
    {
        try
        {
            $result=[];
            // Get Role Access
            $resultClass = new Class_RoleAccessManagement();
            $resultRoleAccess = $resultClass->show($request);
            $result['get_roleAccess'] = $resultRoleAccess['data'];

            // Get Role Access Detail
            $resultClass = new Class_RoleAccessDetail();
            $resultRoleAccess = $resultClass->show($request);
            $result['get_roleAccessDetail'] = $resultRoleAccess['data'];

            return [
                'success' => true,
                'message' => 'Get successful',
                'data'=> $result
            ];
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Model';
            $requestModule['service'] = 'Get-Budget';
            $requestModule['class'] = 'BudgetCoa';
            $requestModule['function'] = 'getBudgetCoa';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }
    }
}
