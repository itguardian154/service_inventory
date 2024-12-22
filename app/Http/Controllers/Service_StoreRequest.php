<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Log\LogError;
use App\Http\Controllers\Model\StoreRequest\StoreRequest;
use App\Models\store_request;

use Carbon\Carbon;
use DateTime;

class Service_StoreRequest extends Controller
{
    public function getStoreRequest(Request $request)
    {
        try
        {
            $module = new StoreRequest();
            $resultModel = $module->getStoreRequest($request); 
            
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
            $requestModule['service'] = 'Get-StoreRequest';
            $requestModule['class'] = 'Service_StoreRequest';
            $requestModule['function'] = 'getStoreRequest';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function insertStoreRequest(Request $request)
    {
        try
        { 
            $result = [];
            $classModel = new StoreRequest();
            $resultModel = $classModel->insertStoreRequest($request); 
            
            if($resultModel['success'])
            {
                $result=response()->json([
                    'status' => 'success',
                    'message' => 'Created Store Requst Successfuly',
                    'data' => $resultModel['data']
                ]);
            }
            else
            {
                $result=response()->json([
                    'status' => 'failed',
                    'message' => 'Error Created Store Requst',
                    'data' => $resultModel['message']
                ]);
            }
            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Insert-StoreRequest';
            $requestModule['class'] = 'Service_StoreRequest';
            $requestModule['function'] = 'insertStoreRequest';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    }

    public function updateStoreRequest(Request $request)
    {
        try
        {
            if (isset($request['status_approve']) && $request['status_approve']!='' ) 
            {
                $classModel = new StoreRequest();
                $result = $classModel->updateApprovalStoreRequest($request); 
            }
            else
            {
                $classModel = new StoreRequest();
                $result = $classModel->updateStoreRequest($request); 
            }
           
            $result=response()->json([
                'status' => 'success',
                'message' => 'Update Store Request Successfuly',
                'data' => $result
            ]);

            return $result;
        } catch (\Exception $ex) {
            # Insert Log Error
            $requestModule=[];
            $requestModule['reff'] = 'Service';
            $requestModule['service'] = 'Update-StoreRequest';
            $requestModule['class'] = 'StoreRequest';
            $requestModule['function'] = 'updateStoreRequest';
            $requestModule['message'] = $ex->getMessage();
            $requestModule['note'] = '-';
            $classModel = new LogError();
            $result = $classModel->insertLogError($requestModule);
            # End Log Error
            return $ex;
        }  
    } 
}
